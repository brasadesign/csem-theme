<?php
	/**
	 * Carrega o ultimo post de cada rede social (instagram, facebook e youtube)
	 *
	 * @author   Matheus Gimenez Petroni <contato@matheusgimenez.com.br>
	 */
	class Load_Last_Post_Social_Networks {
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Imagem do ultimo post de cada rede
		 *
		 * @var string
		 */
		public $image = '';

		/**
		 * Transient de cada rede
		 *
		 * @var array
		 */
		public $transient = '';

		/**
		 * Tempo dos transients (segundos)
		 *
		 * Atualmente 5hrs
		 *
		 * @var int
		 */
		public $transient_time = 2000;

		/**
		 * Initialize the plugin
		 */
		public function __construct() {
			// Adiciona as chamadas AJAX
			add_action( 'wp_ajax_csem_get_last_socials', array( $this, 'do_ajax') );
			add_action( 'wp_ajax_nopriv_csem_get_last_socials', array( $this, 'do_ajax') );
		}
		/**
		 * Do AJAX
		 */
		public function do_ajax() {
			if ( ! isset( $_REQUEST[ 'network'] ) ) {
				wp_die( 'Falta o parametro "network"');
			}
			if ( 'facebook' === $_REQUEST[ 'network' ] ) {
				$this->get_facebook_feed();
				get_template_part( 'section-parts/each-social' );
				wp_die();
			}
			if ( 'youtube' === $_REQUEST[ 'network' ] ) {
				$this->get_youtube_feed();
				get_template_part( 'section-parts/each-social' );
				wp_die();
			}
			if ( 'instagram' === $_REQUEST[ 'network' ] ) {
				$this->get_instagram_feed();
				get_template_part( 'section-parts/each-social' );
				wp_die();
			}
		}
		private function get_instagram_feed() {
			if ( false !== get_transient( 'csem_instagram_transient' ) ) {
				$this->transient = get_transient( 'csem_instagram_transient' );
				$this->image = $this->transient[ 'image' ];
				return;
			}
			$token = coletivo_get_theme_mod( 'coletivo_ultimos_sociais_instagram_token' );
			if ( ! $token ) {
				wp_die( 'Falta o token do instagram' );
				return;
			}
			$instagram_api_url = 'https://api.instagram.com/v1/users/self/media/recent?access_token=' . $token;
			$response = file_get_contents( $instagram_api_url );
			$json = json_decode( $response );
			//var_dump( $response );
			$this->image = $json->data[0]->images->standard_resolution->url;
			$this->transient = array( 'image' => $this->image );
			set_transient( 'csem_instagram_transient', $this->transient, $this->transient_time );
		}

		private function get_youtube_feed() {
			if ( false !== get_transient( 'csem_yt_transient' ) ) {
				$this->transient = get_transient( 'csem_yt_transient' );
				$this->image = $this->transient[ 'image' ];
				return;
			}
			$channel_id = coletivo_get_theme_mod( 'coletivo_ultimos_sociais_yt_id', 'UClnwaE2l2FG0eKWR9Rc610A' );
			$youtube_feed_url = 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $channel_id;
			$response = file_get_contents( $youtube_feed_url );
			//var_dump( $response );
			$xml = new SimpleXMLElement( $response );
			$last = $xml->entry[3];
			$last_id = str_replace( 'yt:video:', '', $last->id );
			$this->image = sprintf( 'https://i1.ytimg.com/vi/%s/hqdefault.jpg', $last_id );
			$this->transient = array( 'image' => $this->image );
			set_transient( 'csem_yt_transient', $this->transient, $this->transient_time );
		}

		/**
		 * Get facebook feed
		 */
		private function get_facebook_feed() {
			$this->image = 'https://scontent.fbjp2-1.fna.fbcdn.net/v/t1.0-9/47421093_2387798797962078_1634059928836505600_n.jpg?_nc_cat=104&_nc_oc=AQmylX6kY680qxpq5LM60r4SJ1IEE1Nsr2ye3CTPklG8KC2h7JEdD3T4JQ3RAuMwl4I&_nc_ht=scontent.fbjp2-1.fna&oh=c4590b6c9af29ccb67d0fc1b12644469&oe=5E3252F6';
			return;
			if ( false !== get_transient( 'csem_fb_transient' ) ) {
				$this->transient = get_transient( 'csem_fb_transient' );
				$this->image = $this->transient[ 'image' ];
				return;
			}
			/* Getting a JSON Facebook Feed

			1. Sign in as a developer at https://developers.facebook.com/
			2. Click "Create New App" at https://developers.facebook.com/apps
			3. Under Apps Settings, find the App ID and App Secret*/

			$app_id = coletivo_get_theme_mod( 'coletivo_ultimos_sociais_fb_appid' );
			if ( ! $app_id ) {
				ẁp_die( 'Preencha o AppID na página nas configurações' );
			}
			$secret = coletivo_get_theme_mod( 'coletivo_ultimos_sociais_fb_secret' );
			if ( ! $secret ) {
				ẁp_die( 'Preencha o AppID na página nas configurações' );
			}
			$link = coletivo_get_theme_mod( 'coletivo_ultimos_sociais_fb_url' );
			if ( ! $link ) {
				ẁp_die( 'Preencha o Link na página nas configurações' );
			}

			/* Configuring a JSON Facebook Feed
			==========================================================================
			1. Find the desired feed ID at http://findmyfacebookid.com/
			2. Set the maximum number of stories to retrieve
			3. Set the seconds to wait between caching the response */
			$feed = coletivo_get_theme_mod( 'coletivo_ultimos_sociais_fb' );
			if ( ! $feed ) {
				ẁp_die( 'Preencha o ID da página nas configurações' );
			}
			$maximum = 1;

			//$authentication = file_get_contents("https://graph.facebook.com/oauth/access_token?grant_type=client_credentials&client_id={$app_id}&client_secret={$secret}");

			//var_dump( $authentication );
			/**
			$authentication = json_decode( $authentication );
			$authentication = http_build_query( $authentication );
			//var_dump( $authentication );
			*/
			$opts = array('http' =>
				array(
					'method'  		=> 'GET',
					'user_agent'	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0'	
				)
			);
			$context = stream_context_create($opts);
			
			$response = file_get_contents($link, false, $context );
			if ( ! $response ) {
				wp_die();
			}
			$doc = new DOMDocument();
			libxml_use_internal_errors(true);
			$doc->loadHTML( $response );

			libxml_clear_errors();

			$xpath = new DOMXPath($doc);

			$classname = 'scaledImageFitWidth';

			$nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			echo $response;
			wp_die();

			if ( ! $nodes ) {
				return;
			}
			$img = $nodes->item(1)->getAttribute('src');
			var_dump( $nodes );
			wp_die();

			//echo $doc->saveHTML();
			
			if ( ! $img ) {
				wp_die();
			}
			
			//var_dump( $response->data[0]->images[6] );
			//var_dump("https://graph.facebook.com/{$feed}/photos/uploaded/?{$authentication}&limit={$maximum}&fields=images,link");
			//wp_die();
			$this->image = $img;
			//echo json_encode( $response );
			$this->transient = array( 'image' => $this->image );
			set_transient( 'csem_fb_transient', $this->transient, $this->transient_time );
		}
		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}



	} // end class Load_Last_Post_Social_Networks();
	global $load_last_post_social_networks;
	$load_last_post_social_networks = new Load_Last_Post_Social_Networks();
