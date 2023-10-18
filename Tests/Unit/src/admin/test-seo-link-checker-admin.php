<?php

namespace ROCKET_WP_CRAWLER\Tests;

use ROCKET_WP_CRAWLER\SEO_Link_Checker_Admin;
use WPMedia\PHPUnit\Unit\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class SEO_Link_Checker_Admin_Test extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp(); 
        Functions\when('update_option')->justReturn(true);
        Functions\when('current_time')->justReturn('2023-10-17 12:00:00');
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_display() {
        $admin = SEO_Link_Checker_Admin::get_instance();

        update_option('seo_crawl_hyperlinks', serialize([
            'homepage' => 'https://example.com',
            'links' => serialize(['https://example.com/link1', 'https://example.com/link2']),
            'crawl_time' => current_time('mysql')
        ]));

        ob_start();
        $admin->display();
        $output = ob_get_clean();
        $this->assertStringContainsString('Crawl Results', $output);
        $this->assertStringContainsString('https://example.com/link1', $output);
    }

    public function test_crawl_homepage() {
        $admin = SEO_Link_Checker_Admin::get_instance();

        Functions\when('wp_remote_get')->justReturn(['body' => '<a href="https://example.com/link1">Link 1</a>']);

        $admin->crawl_homepage();
        $crawl_data = get_option('seo_crawl_hyperlinks');
        $this->assertStringContainsString('https://example.com/link1', $crawl_data['links']);
    }

    public function test_make_homepage_html() {
        $admin = SEO_Link_Checker_Admin::get_instance();
        $admin->make_homepage_html();

        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/homepage.html';

        $this->assertFileExists($file_path);
    }

    public function test_delete_sitemap_file() {
        $admin = SEO_Link_Checker_Admin::get_instance();
        $file_path = ABSPATH . 'sitemap.html';

        file_put_contents($file_path, 'Test content');

        $admin->delete_sitemap_file();

        $this->assertFileNotExists($file_path);
    }
}
