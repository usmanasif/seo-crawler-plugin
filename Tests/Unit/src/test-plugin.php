<?php

namespace ROCKET_WP_CRAWLER\Tests;

use ROCKET_WP_CRAWLER\Rocket_Wpc_Plugin_Class;
use WPMedia\PHPUnit\Unit\TestCase;
use Brain\Monkey\Functions;

class Rocket_Wpc_Plugin_Class_Test extends TestCase {

    public function test_wpc_activate() {
        $user_id = $this->factory->user->create(['role' => 'subscriber']);
        Functions\when('current_user_can')->justReturn(true);
        Rocket_Wpc_Plugin_Class::wpc_activate();
        $this->assertTrue(current_user_can('activate_plugins'));
    }

    public function test_wpc_deactivate() {
        $plugin = new Rocket_Wpc_Plugin_Class();
        Functions\when('current_user_can')->justReturn(true);

        $plugin->wpc_deactivate();
        $this->assertFalse(wp_next_scheduled('seo_link_checker_cron'));
    }

    public function test_run() {
        $plugin = new Rocket_Wpc_Plugin_Class();
        $plugin->run();
        $this->assertTrue(has_action('admin_menu', [$plugin, 'add_admin_menu']));
        $this->assertTrue(has_action('admin_enqueue_scripts', [$plugin, 'enqueue_styles']));
    }
}
