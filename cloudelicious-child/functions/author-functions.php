<?php
add_action( 'show_user_profile', 'oj_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'oj_extra_user_profile_fields' );


function oj_extra_user_profile_fields( $user ) { ?>
    <h3><?php _e("Extra profile information", "blank"); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="job_title"><?php _e("Job Title"); ?></label></th>
        <td>
            <input type="text" name="job_title" id="job_title" value="<?php echo esc_attr( get_the_author_meta( 'job_title', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your job title."); ?></span>
        </td>
    </tr>
    <tr>
        <th><label for="company"><?php _e("Company"); ?></label></th>
        <td>
            <input type="text" name="company" id="company" value="<?php echo esc_attr( get_the_author_meta( 'company', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your company."); ?></span>
        </td>
    </tr>
    </table>
<?php }

add_action( 'personal_options_update', 'save_oj_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_oj_extra_user_profile_fields' );

function save_oj_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }
    update_user_meta( $user_id, 'job_title', $_POST['job_title'] );
    update_user_meta( $user_id, 'company', $_POST['company'] );
}

add_filter( 'user_contactmethods', 'be_hide_profile_fields', 10, 1 );
function be_hide_profile_fields( $contactmethods ) {
    $contactmethods['linkedin']    = 'LinkedIn';
    $contactmethods['wordpress']   = 'Wordpress';
    $contactmethods['youracclaim'] = 'Youracclaim';
    
    return $contactmethods;
}

// showing job title 
// add_action('generate_after_archive_title','my_job_title');
function my_job_title($user){
$html ='<div class="oj-author-job-title">'.esc_attr( get_the_author_meta( 'job_title', $user->ID ) ).' at '.esc_attr( get_the_author_meta( 'company', $user->ID ) ).'</div>';
return $html;
}

// add social media to author description at author archive
// add_action( 'generate_after_archive_description','oj_author_social' );
function oj_author_social($user_id){

$oj_facebook  = esc_attr( get_the_author_meta( 'facebook', $user ) );
$oj_twitter   = esc_attr( get_the_author_meta( 'twitter', $user ) );
$oj_linkedin  = esc_attr( get_the_author_meta( 'linkedin', $user ) );
$oj_wordpress = esc_attr( get_the_author_meta( 'wordpress', $user ) );
$oj_youracclaim    = esc_attr( get_the_author_meta( 'youracclaim', $user ) );
$html = '<div class="oj-social">'.
($oj_facebook ? '<a target="_blank" href="'.$oj_facebook.'"><i class="fa fa-facebook fa- about-us-social-icons fb "></i></a>':'').
($oj_twitter ? '<a target="_blank" href="'.$oj_twitter.'"><i class="fa fa-twitter fa- about-us-social-icons tweet "></i></a>':'').
($oj_linkedin ? '<a target="_blank" href="'.$oj_linkedin.'"><i class="fa fa-linkedin fa- about-us-social-icons linkedin "></i></a>':'').
($oj_wordpress ? '<a target="_blank" href="'.$oj_wordpress.'"><i class="fa fa-wordpress fa- about-us-social-icons wp "></i></a>':'').
($oj_youracclaim ? '<a target="_blank" href="'.$oj_youracclaim.'"><i class="fa fa-angle-double-up fa- about-us-social-icons oj-acclaim "></i></a>':'').
'</div>';
return $html;
}
if ( ! function_exists( 'generate_archive_title' ) ) {
	add_action( 'generate_archive_title', 'generate_archive_title' );
	/**
	 * Build the archive title
	 *
	 * @since 1.3.24
	 */
	function generate_archive_title() {
		if ( ! function_exists( 'the_archive_title' ) ) {
			return;
		}

		$clearfix = is_author() ? ' clearfix' : '';
		?>
		<header class="page-header<?php echo $clearfix; // WPCS: XSS ok, sanitization ok. ?>">
			<?php
			/**
			 * generate_before_archive_title hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_before_archive_title' );
			?>

			<h1 class="page-title">
				<?php the_archive_title(); ?>
			</h1>

			<?php
			/**
			 * generate_after_archive_title hook.
			 *
			 * @since 0.1
			 */
			//do_action( 'generate_after_archive_title' );

			// Show an optional term description.
			$term_description = term_description();
			if ( ! empty( $term_description ) ) {
				printf( '<div class="taxonomy-description">%s</div>', $term_description ); // WPCS: XSS ok, sanitization ok.
			}
			$my_user = get_the_author_meta()->ID;
			if ( get_the_author_meta( 'description' ) && is_author() ) {
				echo '<div class="author-info">' .
				my_job_title($my_user). 
				get_the_author_meta( 'description' ) .
				oj_author_social($my_user).
				'</div>'; // WPCS: XSS ok, sanitization ok.
			}

			/**
			 * generate_after_archive_description hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_after_archive_description' ); ?>
		</header><!-- .page-header -->
		<?php
	}
}
function oj_get_author($atts ) {
extract(
    shortcode_atts(
        array(
            'uid' => 1,
		    'show_social'   => 'true',        
        ), 
        $atts
    )
);
$author_fname   =  get_the_author_meta('first_name',$uid);
$author_lname   =  get_the_author_meta('last_name',$uid);
$author_job     =  get_the_author_meta('job_title',$uid);
$author_company =  get_the_author_meta('company',$uid);
$author_desc    =  get_the_author_meta('description',$uid);
$author_avatar  =  get_avatar($uid,'150',$default, $alt);
$author_social  =  oj_author_social($uid);
$oj_facebook    = esc_attr( get_the_author_meta( 'facebook', $uid ) );
$oj_twitter     = esc_attr( get_the_author_meta( 'twitter', $uid ) );
$oj_linkedin    = esc_attr( get_the_author_meta( 'linkedin', $uid ) );
$oj_wordpress   = esc_attr( get_the_author_meta( 'wordpress', $uid ) );
$oj_youracclaim = esc_attr( get_the_author_meta( 'youracclaim', $uid ) );

$social_html  = '<div class="oj-social">'.
				($oj_facebook ? '<a target="_blank" href="'.$oj_facebook.'"><i class="fa fa-facebook fa- about-us-social-icons fb "></i></a>':'').
				($oj_twitter ? '<a target="_blank" href="'.$oj_twitter.'"><i class="fa fa-twitter fa- about-us-social-icons tweet "></i></a>':'').
				($oj_linkedin ? '<a target="_blank" href="'.$oj_linkedin.'"><i class="fa fa-linkedin fa- about-us-social-icons linkedin "></i></a>':'').
				($oj_wordpress ? '<a target="_blank" href="'.$oj_wordpress.'"><i class="fa fa-wordpress fa- about-us-social-icons wp "></i></a>':'').
				($oj_youracclaim ? '<a target="_blank" href="'.$oj_youracclaim.'"><i class="fa fa-angle-double-up fa- about-us-social-icons oj-acclaim "></i></a>':'').
				'</div>';
// var_dump($author_social);
$html='<div class="oj-top-section">
		<div class="oj-author-img">'.$author_avatar.'</div>
			<div class="author-details">
				<h4><strong><span style="color: #333333;">'.$author_fname.' '.$author_lname.'</span></strong></h4>
				<h5><strong><span>'.$author_job.' at '.$author_company.'</span></strong></h5>
			</div>
		</div>
		<p class="about-us-desc">'.$author_desc.'</p>'.
		$social_html;
return $html;
}
add_shortcode('oj_get_author','oj_get_author');

function oj_get_authors($atts ) {
extract(
    shortcode_atts(
        array(
            'uids' => 1,
		    'show_social'   => 'true',  
		    'col'=>3      
        ), 
        $atts
    )
);
$html = '';
$col =12/$col;
$uids =explode(',', $uids);

$html .='<div class="oj-authors-wrapper">';
foreach ($uids as $uid):
$author_fname   =  get_the_author_meta('first_name',$uid);
$author_lname   =  get_the_author_meta('last_name',$uid);
$author_job     =  get_the_author_meta('job_title',$uid);
$author_company =  get_the_author_meta('company',$uid);
$author_desc    =  get_the_author_meta('description',$uid);
$author_avatar  =  get_avatar($uid,'150',$default, $alt);
$author_social  =  oj_author_social($uid);
$oj_facebook    = esc_attr( get_the_author_meta( 'facebook', $uid ) );
$oj_twitter     = esc_attr( get_the_author_meta( 'twitter', $uid ) );
$oj_linkedin    = esc_attr( get_the_author_meta( 'linkedin', $uid ) );
$oj_wordpress   = esc_attr( get_the_author_meta( 'wordpress', $uid ) );
$oj_youracclaim = esc_attr( get_the_author_meta( 'youracclaim', $uid ) );

$social_html  = '<div class="oj-social">'.
				($oj_facebook ? '<a target="_blank" href="'.$oj_facebook.'"><i class="fa fa-facebook fa- about-us-social-icons fb "></i></a>':'').
				($oj_twitter ? '<a target="_blank" href="'.$oj_twitter.'"><i class="fa fa-twitter fa- about-us-social-icons tweet "></i></a>':'').
				($oj_linkedin ? '<a target="_blank" href="'.$oj_linkedin.'"><i class="fa fa-linkedin fa- about-us-social-icons linkedin "></i></a>':'').
				($oj_wordpress ? '<a target="_blank" href="'.$oj_wordpress.'"><i class="fa fa-wordpress fa- about-us-social-icons wp "></i></a>':'').
				($oj_youracclaim ? '<a target="_blank" href="'.$oj_youracclaim.'"><i class="fa fa-angle-double-up fa- about-us-social-icons oj-acclaim "></i></a>':'').
				'</div>';

$html .='<div class="col-'.$col.'">
			<div class="about-us">';
$html.='<div class="oj-top-section">
		<div class="oj-author-img">'.$author_avatar.'</div>
			<div class="author-details">
				<h4><strong><span style="color: #333333;">'.$author_fname.' '.$author_lname.'</span></strong></h4>
				<h5><strong><span>'.$author_job.' at '.$author_company.'</span></strong></h5>
			</div>
		</div>
		<p class="about-us-desc">'.$author_desc.'</p>'.
		$social_html;

$html .='	</div>
		 </div>';
endforeach;
$html .='</div>';

return $html;
}
add_shortcode('oj_get_authors','oj_get_authors');
?>