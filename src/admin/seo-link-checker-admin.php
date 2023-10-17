<?php
namespace ROCKET_WP_CRAWLER;

class SEO_Link_Checker_Admin
{

    private $results_option_name = 'seo_crawl_hyperlinks';
    private static $instance;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        add_action('wp_ajax_run_crawl', array($this, 'ajax_crawl_homepage'));
        add_action('seo_link_checker_cron', array($this, 'crawl_homepage'));
    }

    public function display()
    {
        echo '<h3>Crawl Results</h3>';
        if(isset($_POST['init_crawl'])){
            $this->crawl_homepage();
            Rocket_Wpc_Plugin_Class::schedule_crawl();
        }
        
        $results = get_option($this->results_option_name);
        if($results){
            $c = unserialize($results);
            echo '<p><strong>Last Crawl: </strong>'.$c["crawl_time"].'</p>';
            $results = unserialize($c["links"]);

            echo '<table>';
                    echo '<tr>';
                        echo '<th>Page URL</th>';
                        echo '<th>Hyper Link</th>';
                    echo '<tr>';
                foreach ($results as $result) {
                    echo '<tr>';
                        echo '<td>'. home_url() .'</td>';
                        echo '<td>' . $result . '</td>';
                    echo '<tr>';

                }
            echo '</table>';
        }else {
            echo '<p>No crawl results available.</p>';
        }
        echo '<form method="post" name="init_crawl" action="">
                <input type="submit" value="Initiate Crawl" class="initate-crawl-btn" name="init_crawl">
              </form>';
    }

    private function crawl_homepage()
    {
        $page_url = home_url();
        $page_content = file_get_contents($page_url);    
        $pattern = '/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>/i';
        preg_match_all($pattern, $page_content, $matches);
        $hyperlinks = $matches[1];
        $hyperlinks = array_unique($hyperlinks);

        $data = array(
            'homepage' => $page_url,
            'links' => serialize($hyperlinks),
            'crawl_time' => current_time('mysql'),
        );
        
        $serialized_data = serialize($data);
        update_option('seo_crawl_hyperlinks',$serialized_data);
        $this->make_homepage_html();
        $this->delete_sitemap_file();
    }

    function make_homepage_html(){
        try{
            $wp_page_url = home_url();
            $wp_page_content = file_get_contents($wp_page_url);

            if ($wp_page_content !== false) {
                $upload_dir = wp_upload_dir();
                $file_path = $upload_dir['basedir'] . '/homepage.html';
                $saved = file_put_contents($file_path, $wp_page_content);
            } 
        } catch(\Exception $e){
            return false;
        }
    }

    function delete_sitemap_file(){
        try{
            $file_path = ABSPATH . 'sitemap.html';
            if (file_exists($file_path)) {
                unlink($file_path);
            } 
        } catch(\Exception $e){
            return false;
        }
    }
}