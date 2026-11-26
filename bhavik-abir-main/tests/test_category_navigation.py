"""Category navigation and supply-page presentation contracts."""
import re
import unittest
from pathlib import Path
from test_supply_pages import MATERIALS, GRADES, Structure, source_for

ROOT = Path(__file__).resolve().parents[1]


class CategoryNavigationTests(unittest.TestCase):
    def assert_all_material_destinations(self, source):
        for material in MATERIALS:
            for grade in (None,) + GRADES:
                slug = material.lower() + (f'-{grade}' if grade else '')
                self.assertIn(f"home_url( '/{slug}/' )", source)

    def test_home_and_about_have_static_category_sections(self):
        for page in ('home', 'about'):
            with self.subTest(page=page):
                source = (ROOT / f'page-template/template-{page}.php').read_text()
                category = re.search(r'<section class="ab-section ab-product-categories".*?</section>', source, re.S).group(0)
                self.assert_all_material_destinations(category)
                self.assertNotIn('foreach', category)
                self.assertNotIn('array(', category)
                self.assertEqual(category.count('class="ab-category-card"'), 28)
                self.assertEqual(category.count('class="ab-category-panel"'), 7)
                self.assertEqual(category.count('class="ab-category-tab '), 7)
                self.assertIn('data-initial-material="hdpe"', category)
                self.assertIn('aria-label="Previous material"', category)
                self.assertIn('aria-label="Next material"', category)
                self.assertNotRegex(category, r'class="ab-category-panel"[^>]*\bhidden\b')
                parser = Structure()
                parser.feed(re.sub(r'<\?php.*?\?>', '', category, flags=re.S))
                self.assertEqual(parser.errors, [])
                self.assertEqual(parser.stack, [])
                ids = re.findall(r'\bid="([^"]+)"', category)
                self.assertEqual(len(ids), len(set(ids)))
                for target in re.findall(r'<use href="#([^"]+)"', category):
                    self.assertIn(target, ids)

    def test_independent_grain_images_and_borderless_full_height_styles(self):
        url = 'https://bhavikenterprises.in/wp-content/uploads/2026/11/image-grains.png'
        for page in ('home', 'about'):
            source = (ROOT / f'page-template/template-{page}.php').read_text()
            images = re.findall(r'<img data-category-grains="([^"]+)" src="([^"]+)"', source)
            self.assertEqual(len(images), 7)
            self.assertEqual({material for material, _ in images}, {m.lower() for m in MATERIALS})
            self.assertTrue(all(src == url for _, src in images))
        css = (ROOT / 'assets/css/product-categories.css').read_text()
        art = re.search(r'\.ab-product-categories \.ab-category-card-art\s*\{([^}]+)', css).group(1)
        for rule in ('height: 100%;', 'min-height: 100%;', 'object-fit: cover;'):
            self.assertIn(rule, art)
        grain = re.search(r'\.ab-product-categories \.ab-category-tab img\s*\{([^}]+)', css).group(1)
        for rule in ('object-fit: contain;', 'object-position: center bottom;', 'filter: none;', 'mask-image: none;', 'opacity: 1;'):
            self.assertIn(rule, grain)
        self.assertNotRegex(css, r'box-shadow:[^;]*\b0 0 0 [1-9]')
        self.assertNotIn('blur(', css)

    def test_compact_tabs_and_desktop_only_two_by_two_cards(self):
        css = (ROOT / 'assets/css/product-categories.css').read_text()
        desktop = css.split('@media (min-width: 1200px) {', 1)[1].split('\n}', 1)[0]
        self.assertIn('grid-template-columns: repeat(2, minmax(0, 1fr))', desktop)
        self.assertIn('width: 50%;', desktop)
        self.assertIn('polygon(0 0, 70% 0, 50% 100%, 0 100%)', desktop)
        self.assertRegex(css, r'\.ab-category-tab\s*\{[^}]*min-height: 176px;[^}]*padding: 14px 12px 84px;')
        self.assertRegex(css, r'\.ab-category-tab img\s*\{[^}]*height: 80px;')
        phone = css.split('@media (max-width: 700px) {', 1)[1].split('\n}', 1)[0]
        self.assertIn('grid-template-columns: minmax(0, 1fr)', phone)

    def test_soft_bevels_and_twenty_percent_stronger_section_tint(self):
        css = (ROOT / 'assets/css/product-categories.css').read_text()
        # Original end-stop is a 20% blend of #bedcd7 over white; 24% is
        # a 20% relative increase in tint strength, not 20% darker text/UI.
        tint = tuple(round(255 + (channel - 255) * .24) for channel in (190, 220, 215))
        self.assertEqual(tint, (239, 247, 245))
        self.assertIn('background: linear-gradient(155deg, #fff, #eff7f5);', css)
        bevel = re.search(r'\.ab-category-card::after\s*\{([^}]+)', css).group(1)
        for rule in ('position: absolute;', 'inset: 0;', 'z-index: 1;', 'border-radius: inherit;', 'pointer-events: none;'):
            self.assertIn(rule, bevel)
        self.assertIn('inset 0 2px 5px', bevel)
        self.assertIn('inset 0 -4px 10px', bevel)
        self.assertNotRegex(css, r'box-shadow:[^;]*\b0 0 0 [1-9]')

    def test_mega_menu_covers_all_material_and_supply_pages(self):
        source = (ROOT / 'template-parts/products-mega-menu.php').read_text()
        self.assert_all_material_destinations(source)
        self.assertEqual(source.count('class="ab-mega-material"'), 7)
        markup = re.sub(r'<\?php.*?\?>', '', source, flags=re.S)
        parser = Structure()
        parser.feed(markup)
        self.assertEqual(parser.errors, [])
        self.assertEqual(parser.stack, [])

    def test_mega_menu_uses_self_contained_svg_artwork(self):
        source = (ROOT / 'template-parts/products-mega-menu.php').read_text()
        self.assertNotRegex(source, r'<(?:img|image|canvas)\b')
        self.assertNotIn('data:image', source)
        self.assertEqual(source.count('class="ab-mega-material-head"'), 7)
        self.assertEqual(source.count('class="ab-mega-chevron"'), 28)
        ids = set(re.findall(r'id="(ab-mega-[^"]+)"', source))
        references = set(re.findall(r'href="#(ab-mega-[^"]+)"', source))
        self.assertTrue(references.issubset(ids), references - ids)
        for route in ('/products/', '/product-range/', '/contact-us/'):
            self.assertIn(f"home_url( '{route}' )", source)
        self.assertIn('$extra_links', source)

    def test_supply_switch_typography_is_shared_at_every_size(self):
        source = (ROOT / 'assets/css/material.css').read_text()
        label = re.search(r'\.ab-material-switch-label\s*\{([^}]+)', source).group(1)
        link = re.search(r'\.ab-material-switch-links a\s*\{([^}]+)', source).group(1)
        for rule in (label, link):
            self.assertIn('font-size: 17px;', rule)
            self.assertIn('font-weight: 600;', rule)
            self.assertIn('letter-spacing: normal;', rule)
        self.assertIn('font-family: inherit;', link)
        self.assertNotRegex(source, r'\.ab-material-switch-links a\s*\{[^}]*font-size: (?:13|14)px')
        self.assertNotIn('.ab-material-grade-page .ab-material-switch', source)

    def test_breadcrumbs_and_repeated_supply_note_are_removed(self):
        for material in MATERIALS:
            for grade in (None,) + GRADES:
                with self.subTest(material=material, grade=grade):
                    source = source_for(material, grade)
                    self.assertNotIn('ab-material-breadcrumb', source)
                    self.assertNotIn('ab-supply-route-note', source)
                    self.assertNotIn('Grade, availability, documents and application suitability are confirmed per enquiry. No regulatory approval is implied.', source)

    def test_new_typography_contracts(self):
        source = (ROOT / 'assets/css/material.css').read_text()
        for selector, size in (('.ab-supply-route-card h3', 22), ('.ab-supply-route-card p', 17), ('.ab-supply-route-kicker', 17), ('.ab-supply-route-card .ab-link', 17), ('.ab-grade-brief p', 17)):
            self.assertRegex(source, re.escape(selector) + r'\s*\{[^}]*font-size:\s*' + str(size) + r'px;')
        self.assertIn('.ab-material-grade-page .ab-link', source)


if __name__ == '__main__':
    unittest.main()
