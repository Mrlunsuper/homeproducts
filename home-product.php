<?php
/**
 * Plugin Name: Home Prouduct
 */
 add_action( 'admin_enqueue_scripts', 'rudr_select2_enqueue' );
 function rudr_select2_enqueue(){
  
    wp_enqueue_style( 'select2css', plugins_url().'/homeproduct/select2.min.css', false, '1.0', 'all' );
    wp_enqueue_script( 'select2', plugins_url().'/homeproduct/select2.full.min.js', array( 'jquery' ), '1.0', true );
  
     // please create also an empty JS file in your theme directory and include it too
     wp_enqueue_script('homeproductjs', plugins_url() . '/homeproduct/home-product.js', array( 'jquery') ); 
  
 }
include 'custom-field.php';
class Home_Archive extends WP_Widget {
 
    function __construct() {
 
        parent::__construct(
            'home-archive',  // Base ID
            'Danh mục trang chủ'   // Name
        );
 
        add_action( 'widgets_init', function() {
            register_widget( 'Home_Archive' );
        });
 
    }
 
    public $args = array(
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
 
    public function widget( $args, $instance ) {
 
        echo $args['before_widget'];
 
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
       
        echo '<div class="home-archive">';
        
        if(!empty($instance['archive'])) : foreach($instance['archive'] as $arc):
            $thumbnail_id = get_term_meta( $arc, 'thumbnail_id', true ); 
            $image = wp_get_attachment_url( $thumbnail_id ); 
            $taxonomy = get_term_by('id',$arc,'product_cat');
            $term_link = get_term_link($taxonomy,'product_cat');
            echo '<div class="home-archive-item">';
            printf('<a href="%1$s"/>',$term_link);
            printf('<img src="%1$s" title="$2$s" class="lazy"/>',$image,$taxonomy->name);
            printf('<h3>%1$s</h3>',$taxonomy->name);
            echo '</a></div>';
        endforeach;endif;
 
        echo '</div>';
 
        echo $args['after_widget'];
 
    }
 
    public function form( $instance ) {
 
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'sdt' );
        $archive = ! empty( $instance['archive'] ) ? $instance['archive'] : array();
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Tiêu đề:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'archive' ) ); ?>"><?php echo esc_html__( 'Danh mục:', 'sdt' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'archive' ) ); ?>" class="home-archives" name="<?php echo esc_attr( $this->get_field_name( 'archive' ) ); ?>[]" multiple="multiple" style="width: 100%;">

                <?php 
                    $orderby = 'name';
                    $order = 'asc';
                    $hide_empty = false ;
                    $cat_args = array(
                        'orderby'    => $orderby,
                        'order'      => $order,
                        'hide_empty' => $hide_empty,
                    );
                    
                    $product_categories = get_terms( 'product_cat', $cat_args );
                    foreach($product_categories as $pc) :
                ?>
                <option <?php selected(in_array($pc->term_id,$archive),true); ?> value="<?php echo $pc->term_id; ?>"><?php echo $pc->name; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" class="input_hidden" value="16" name="<?php echo esc_attr( $this->get_field_id( 'archive' ) ); ?>" id="archive">
        </p>
        <?php
 
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
 
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['archive'] = ( !empty( $new_instance['archive'] ) ) ? $new_instance['archive'] : array();
 
        return $instance;
    }
 
}
class HomeProduct extends WP_Widget {
 
    function __construct() {
 
        parent::__construct(
            'home-product',  // Base ID
            'Sản phẩm trang chủ'   // Name
        );
 
        add_action( 'widgets_init', function() {
            register_widget( 'HomeProduct' );
        });
 
    }
 
    public $args = array(
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
 
    public function widget( $args, $instance ) {
 
        echo $args['before_widget'];
 
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
       
        printf('<div class="%1$s">',$instance['type']);
        
        if(!empty($instance['products'])) :
            $prods = new WP_Query(array(
                'post_type' =>'product',
                'post__in' =>$instance['products']
            ));
            while($prods->have_posts()) : $prods->the_post();
                wc_get_template_part('content','product');
        endwhile;wp_reset_query();endif;
 
        echo '</div>';
 
        echo $args['after_widget'];
 
    }
 
    public function form( $instance ) {
 
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'sdt' );
        $products = ! empty( $instance['products'] ) ? $instance['products'] : array();
        $type = ! empty( $instance['type'] ) ? $instance['type'] : '';
        $types = ['type-accessories'=>'Phụ kiện','type-products'=>'Sản phẩm','type-scroll'=>'Trượt ngang'];
        ?>
        <p?>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Tiêu đề:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'products' ) ); ?>"><?php echo esc_html__( 'Sản phẩm:', 'sdt' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'products' ) ); ?>" class="home-products" name="<?php echo esc_attr( $this->get_field_name( 'products' ) ); ?>[]" multiple="multiple" style="width: 100%;">
                <?php if(!empty($products)) : foreach($products as $prod) : ?>
                    <option selected="selected" value="<?php echo $prod; ?>"><?php echo get_the_title($prod); ?></option> 
                <?php endforeach;endif; ?>
            </select>
            <input type="hidden" class="input_hidden" value="16" name="<?php echo esc_attr( $this->get_field_id( 'products' ) ); ?>" id="archive">
       </p>
       <p>
        <label for="<?php echo esc_attr($this->get_field_id('type')); ?>" >Chọn kiểu hiển thị
            <select name="<?php echo esc_attr($this->get_field_name('type')); ?>" id="<?php echo esc_attr($this->get_field_id('type')); ?>"> 
                <?php foreach($types as $key => $val) : ?>
                    <option <?php selected($type==$key,true); ?> value="<?php echo $key ?>"><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        </p>
        <?php
 
    }
 
    public function update( $new_instance, $old_instance ) {
 
        $instance = array();
 
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['products'] = ( !empty( $new_instance['products'] ) ) ? $new_instance['products'] : array();
        $instance['type'] = ( !empty( $new_instance['type'] ) ) ? $new_instance['type'] : '';
        
        return $instance;
    }
 
}
$my_widget = new Home_Archive();
$homeprd = new HomeProduct();

add_action( 'wp_ajax_searchProducts', 'rudr_get_posts_ajax_callback' ); // wp_ajax_{action}
function rudr_get_posts_ajax_callback(){
 
	// we will pass post IDs and titles to this array
	$return = array();
 
	// you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
	$search_results = new WP_Query( array( 
		's'=> $_GET['q'], // the search query
		'post_status' => 'publish', // if you don't want drafts to be returned
		'ignore_sticky_posts' => 1,
		'posts_per_page' => 50 // how much to show at once
	) );
	if( $search_results->have_posts() ) :
		while( $search_results->have_posts() ) : $search_results->the_post();	
			// shorten the title a little
			$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
			$return[] = array( $search_results->post->ID, $title ); // array( Post ID, Post Title )
		endwhile;
	endif;
	echo json_encode( $return );
	die;
}
?>