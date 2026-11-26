"""Run with: PYTHONDONTWRITEBYTECODE=1 python3 -m unittest discover -s tests -v"""
import re
import unittest
from collections import Counter
from html.parser import HTMLParser
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
MATERIALS = ('LDPE', 'HDPE', 'PP', 'PVC', 'PS', 'ABS', 'EVA')
GRADES = ('prime', 'offgrade', 'recycled', 'scrap-regrind')
LABELS = ('Prime / Virgin', 'Off-grade', 'Recycled', 'Scrap / Regrind')


def source_for(material, grade=None):
    name = ('template-material.php' if material == 'HDPE' else f'template-material-{material}.php') if grade is None else f'template-material-{material}-{grade}.php'
    return (ROOT / 'page-template' / name).read_text()


class Structure(HTMLParser):
    """Check nesting on standalone templates, ignoring PHP and HTML void elements."""
    VOID = set('area base br col embed hr img input link meta param source track wbr'.split())

    def __init__(self):
        super().__init__()
        self.stack = []
        self.errors = []

    def handle_starttag(self, tag, attrs):
        if tag not in self.VOID:
            self.stack.append(tag)

    def handle_startendtag(self, tag, attrs):
        pass

    def handle_endtag(self, tag):
        if not self.stack or self.stack[-1] != tag:
            self.errors.append(f'unmatched closing {tag}; stack: {self.stack[-5:]}')
        else:
            self.stack.pop()


class SupplyPagesTests(unittest.TestCase):
    def test_all_35_pages_have_real_five_way_navigation(self):
        for material in MATERIALS:
            for grade in (None,) + GRADES:
                with self.subTest(material=material, grade=grade):
                    source = source_for(material, grade)
                    switch = source.split('<nav class="ab-material-switch"', 1)[1].split('</nav>', 1)[0]
                    slugs = [material.lower()] + [f'{material.lower()}-{item}' for item in GRADES]
                    for slug in slugs:
                        self.assertIn(f"home_url( '/{slug}/' )", switch)
                    self.assertEqual(switch.count('aria-current="page"'), 1)
                    current = material.lower() + (f'-{grade}' if grade else '')
                    self.assertRegex(switch, re.escape(f"home_url( '/{current}/' )") + r'.*?aria-current="page"')
                    self.assertNotIn('role="tab', switch)
                    self.assertNotIn('foreach', switch)

    def test_standalone_templates_are_static_and_well_formed(self):
        for material in MATERIALS:
            for grade, label in zip(GRADES, LABELS):
                with self.subTest(material=material, grade=grade):
                    source = source_for(material, grade)
                    self.assertNotIn('array(', source)
                    self.assertNotIn('foreach', source)
                    self.assertNotIn('the_content(', source)
                    self.assertIn(f'<option selected>{label}</option>', source)
                    self.assertIn(f'name="enquiry_page" value="{material.lower()}-{grade}"', source)
                    self.assertIn('id="primary-enquiry"', source)
                    self.assertIn('id="datasheet-upload"', source)
                    markup = re.sub(r'<\?php.*?\?>', '', source, flags=re.S)
                    self.assertEqual(len(re.findall(r'<h1\b', markup)), 1)
                    ids = Counter(re.findall(r'\bid="([^"]+)"', markup))
                    self.assertFalse([key for key, count in ids.items() if count > 1])
                    parser = Structure()
                    parser.feed(markup)
                    self.assertEqual(parser.errors, [])
                    self.assertEqual(parser.stack, [])

    def test_all_routes_are_explicitly_provisioned_with_metadata(self):
        installer = (ROOT / 'inc/material-supply-pages.php').read_text()
        for material in MATERIALS:
            for grade in (None,) + GRADES:
                slug = material.lower() + (f'-{grade}' if grade else '')
                self.assertEqual(installer.count(f"ab_ensure_supply_landing_page( '{slug}',"), 1)
        self.assertIn("'post_status' => 'publish'", installer)
        self.assertIn("'ab_meta_title'", installer)
        self.assertIn("'ab_meta_description'", installer)

    def test_ps_and_two_line_logo(self):
        source = source_for('PS')
        copy = re.sub(r'<[^>]+>', '', re.sub(r'<\?php.*?\?>', '', source, flags=re.S))
        self.assertIn('Polystyrene', copy)
        self.assertIn('GPPS', copy)
        self.assertIn('HIPS', copy)
        self.assertNotIn('HDPE', copy)
        for file in ('header.php', 'footer.php'):
            source = (ROOT / file).read_text()
            self.assertIn('<span class="ab-logo-name">Bhavik</span>', source)
            self.assertIn('<span class="ab-logo-descriptor">Enterprises</span>', source)


if __name__ == '__main__':
    unittest.main()
