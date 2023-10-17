<?php
namespace ROCKET_WP_CRAWLER;

/**
 * Admin class for SEO Link Checker.
 *
 * This class defines all the necessary code for the admin side of the plugin.
 */
class SEO_Link_Checker_Admin {

	/**
	 * Variable for hyperlinks option name
	 *
	 * @var string
	 */
	private $results_option_name = 'seo_crawl_hyperlinks';

	/**
	 * Variable for Admin class instance
	 *
	 * @var SEO_Link_Checker_Admin
	 */
	private static $instance;

	/**
	 * Get instance of the class.
	 *
	 * @return SEO_Link_Checker_Admin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_run_crawl', array( $this, 'ajax_crawl_homepage' ) );
		add_action( 'seo_link_checker_cron', array( $this, 'crawl_homepage' ) );
	}

	/**
	 * Display results.
	 */
	public function display() {
		echo '<h3>Crawl Results</h3>';
		if ( isset( $_POST['init_crawl'] ) && check_admin_referer( 'seo_crawl_nonce_action', 'seo_crawl_nonce_field' ) ) {
			$this->crawl_homepage();
			Rocket_Wpc_Plugin_Class::schedule_crawl();
		}

		$results = get_option( $this->results_option_name );

		if ( $results ) {
			$data = unserialize( $results );
			echo '<p><strong>Last Crawl: </strong>' . esc_html( $data['crawl_time'] ) . '</p>';
			$results = unserialize( $data['links'] );

			echo '<table>';
					echo '<tr>';
						echo '<th>Page URL</th>';
						echo '<th>Hyper Link</th>';
					echo '<tr>';
			foreach ( $results as $result ) {
				echo '<tr>';
					echo '<td>' . esc_html( home_url() ) . '</td>';
					echo '<td>' . esc_html( $result ) . '</td>';
				echo '<tr>';
			}
			echo '</table>';
		} else {
			echo '<p>No crawl results available.</p>';
		}
		echo '<form method="post" name="init_crawl" action="">';
		wp_nonce_field( 'seo_crawl_nonce_action', 'seo_crawl_nonce_field' );
		echo '<input type="submit" value="Initiate Crawl" class="initate-crawl-btn" name="init_crawl">
              </form>';
	}

	/**
	 * Crawl homepage.
	 */
	private function crawl_homepage() {
		$page_url     = home_url();
		$response     = wp_remote_get( $page_url );
		$page_content = wp_remote_retrieve_body( $response );
		$pattern      = '/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>/i';
		preg_match_all( $pattern, $page_content, $matches );
		$hyperlinks = $matches[1];
		$hyperlinks = array_unique( $hyperlinks );

		$data = array(
			'homepage'   => $page_url,
			'links'      => serialize( $hyperlinks ),
			'crawl_time' => current_time( 'mysql' ),
		);

		$serialized_data = serialize( $data );
		update_option( 'seo_crawl_hyperlinks', $serialized_data );
		$this->make_homepage_html();
		$this->delete_sitemap_file();
	}

	/**
	 * Make homepage HTML.
	 */
	private function make_homepage_html() {
		try {
			$wp_page_url     = home_url();
			$response        = wp_remote_get( $wp_page_url );
			$wp_page_content = wp_remote_retrieve_body( $response );

			if ( false !== $wp_page_content ) {
				$upload_dir = wp_upload_dir();
				$file_path  = $upload_dir['basedir'] . '/homepage.html';

				global $wp_filesystem;
				if ( ! $wp_filesystem ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
					WP_Filesystem();
				}
				$wp_filesystem->put_contents( $file_path, $wp_page_content );
			}
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Delete sitemap file.
	 */
	private function delete_sitemap_file() {
		try {
			$file_path = ABSPATH . 'sitemap.html';
			if ( file_exists( $file_path ) ) {
				wp_delete_file( $file_path );
			}
		} catch ( \Exception $e ) {
			return false;
		}
	}
}
