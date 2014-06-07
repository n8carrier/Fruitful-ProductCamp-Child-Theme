<?php
/**
 * Template Name: Edit Registration
 * Display edit registration form
 *
 * @package WordPress
 * @subpackage Fruitful theme
 * @since Fruitful theme 1.0
 */

 /* Get user info. */
global $current_user, $wp_roles;
get_currentuserinfo();

/* Load the registration file. */
require_once( ABSPATH . WPINC . '/registration.php' );
$error = array();    
/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    /* Update user password. */
    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
        if ( $_POST['pass1'] == $_POST['pass2'] )
            wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
        else
            $error[] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
    }

    /* Update user information. */
    if ( !empty( $_POST['url'] ) )
        wp_update_user( array( 'ID' => $current_user->ID, 'user_url' => esc_url( $_POST['url'] ) ) );
    if ( !empty( $_POST['nickname'] ) )
        wp_update_user( array( 'ID' => $current_user->ID, 'nickname' => esc_attr( $_POST['nickname'] ) ) );
    if ( !empty( $_POST['display_name'] ) )
        wp_update_user( array( 'ID' => $current_user->ID, 'display_name' => esc_attr( $_POST['display_name'] ) ) );
    if ( !empty( $_POST['email'] ) ){
        if (!is_email(esc_attr( $_POST['email'] )))
            $error[] = __('The Email you entered is not valid.  please try again.', 'profile');
        elseif(email_exists(esc_attr( $_POST['email'] )) != $current_user->id )
            $error[] = __('This email is already used by another user.  try a different one.', 'profile');
        else{
            wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
        }
    }

    if ( !empty( $_POST['first-name'] ) )
        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
    if ( !empty( $_POST['last-name'] ) )
        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
    if ( !empty( $_POST['description'] ) )
        update_user_meta( $current_user->ID, 'description', esc_attr( $_POST['description'] ) );

    /* Redirect so the page will show updated info.*/
  /*I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ */
    if ( count($error) == 0 ) {
        //action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink() );
        exit;
    }
}
 
get_header(); ?>
		<div class="eleven columns alpha">
			<div id="primary" class="content-area">
				<div id="content" class="site-content" role="main">
				
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> page type-page status-publish hentry">
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header><!-- .entry-header -->
						<div class="entry-content entry">
							<?php the_content(); ?>
							<?php if ( !is_user_logged_in() ) : ?>
									<p class="warning">
										<?php _e('You must be logged in to edit your profile.', 'profile'); ?>
									</p><!-- .warning -->
							<?php else : ?>
								<?php if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
								<link href="http://yui.yahooapis.com/pure/0.4.1/forms-min.css" rel="stylesheet" />
								<form method="post" id="adduser" action="<?php the_permalink(); ?>" class="pure-form pure-form-aligned">
									<h4>Ticket Information</h4>
									<div class="pure-control-group">
										<label for="first-name">*First Name</label>
										<input class="pure-input-1-3" id="first-name" type="text" name="first-name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>">
									</div>
									<div class="pure-control-group">
										<label for="last-name">*Last Name</label>
										<input class="pure-input-1-3" id="last-name" type="text" name="last-name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>">
									</div>
									<div class="pure-control-group">
										<label for="rpr_company">*Company</label>
										<input class="pure-input-1-3" type="text" name="rpr_company" id="rpr_company" value="<?php the_author_meta( 'rpr_company', $current_user->ID ); ?>" class="regular-text">
									</div>
									<div class="pure-control-group">
										<label for="rpr_job_title">*Job Title</label>
										<input class="pure-input-1-3" type="text" name="rpr_job_title" id="rpr_job_title" value="<?php the_author_meta( 'rpr_job_title', $current_user->ID ); ?>" class="regular-text">
									</div>
									<div class="pure-control-group">
										<label for="email">*Email</label>
										<input class="pure-input-1-3" id="display_email" type="text" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" disabled="disabled" />
									</div>
									<div class="pure-control-group" style="display:none">
										<label for="email">*Email</label>
										<input class="pure-input-1-3" id="email" type="text" name="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>"  />
									</div>
									<div class="pure-control-group">
										<label for="rpr_ticket_type">Ticket Type</label>
										<select class="pure-input-1-3" name="rpr_ticket_type" id="rpr_ticket_type" current_ticket_type="<?php the_author_meta( 'rpr_ticket_type', $current_user->ID ); ?>">
											<option value="Participant" id="rpr_ticket_type-participant">Participant</option>
											<option value="Volunteer" id="rpr_ticket_type-volunteer">Volunteer</option>
											<option value="CANCELLED" id="rpr_ticket_type-cancelled" style="display:none">CANCELLED</option>
										</select>
									</div>
									<div id="volunteer_interest_group" class="pure-control-group" style="display:none">
										<label id="rpr_volunteer_interest-label" for="rpr_volunteer_interest">Volunteer Interest</label>
										<textarea name="rpr_volunteer_interest" id="rpr_volunteer_interest" rows="2" style="width: 400px;font-size:14px"><?php the_author_meta( 'rpr_volunteer_interest', $current_user->ID ); ?></textarea>
									</div>
									<div class="pure-control-group">
										<label id="rpr_t-shirt-label" for="rpr_t-shirt">*T-shirt</label>
										<select class="pure-input-1-3" id="rpr_t-shirt" name="rpr_t-shirt" current_t-shirt="<?php the_author_meta( 'rpr_t-shirt', $current_user->ID ); ?>">
											<option id="rpr_t-shirt-mens_-_s" value="Mens - S">Men's - S</option>
											<option id="rpr_t-shirt-mens_-_m" value="Mens - M">Men's - M</option>
											<option id="rpr_t-shirt-mens_-_l" value="Mens - L">Men's - L</option>
											<option id="rpr_t-shirt-mens_-_xl" value="Mens - XL">Men's - XL</option>
											<option id="rpr_t-shirt-womens_-_s" value="Womens - S">Women's - S</option>
											<option id="rpr_t-shirt-womens_-_m" value="Womens - M">Women's - M</option>
											<option id="rpr_t-shirt-womens_-_l" value="Womens - L">Women's - L</option>
											<option id="rpr_t-shirt-other_specify_below" value="Other (specify below)">Other (specify below)</option>
											<option id="rpr_t-shirt-none" value="None">None</option>
										</select>
									</div>									
									<div id="t-shirt_other_group" class="pure-control-group" style="display:none">
										<label id="rpr_t-shirt_other-label" for="rpr_t-shirt_other">T-shirt - Other</label>
										<textarea name="rpr_t-shirt_other" id="rpr_t-shirt_other" rows="2" style="width: 400px;font-size:14px"><?php the_author_meta( 'rpr_t-shirt_other', $current_user->ID ); ?></textarea>
									</div>
									<div class="pure-control-group">
										<label id="rpr_dietary_restrictions-label" for="rpr_dietary_restrictions">*Dietary Restrictions</label>
										<select class="pure-input-1-3" id="rpr_dietary_restrictions" name="rpr_dietary_restrictions" current_dietary_restrictions="<?php the_author_meta( 'rpr_dietary_restrictions', $current_user->ID ); ?>">
											<option id="rpr_dietary_restrictions-none" value="None">None</option>
											<option id="rpr_dietary_restrictions-vegetarian" value="Vegetarian">Vegetarian</option>
											<option id="rpr_dietary_restrictions-other_specify_below" value="Other (specify below)">Other (specify below)</option>
										</select>
									</div>
									<div id="dietary_other_group" class="pure-control-group" style="display:none">
										<label id="rpr_dietary_restrictions_other-label" for="rpr_dietary_restrictions_other">Dietary Restrictions - Other</label>
										<textarea name="rpr_dietary_restrictions_other" id="rpr_dietary_restrictions_other" rows="2" style="width: 400px;font-size:14px"><?php the_author_meta( 'rpr_dietary_restrictions_other', $current_user->ID ); ?></textarea>
									</div>
									<h4>ProductCampProvo.org Account Information</h4>
									<p style="font-size:80%">Account information used to login to ProductCampProvo.org and propose/vote for sessions.</p>
									<div class="pure-control-group">
										<label for="user_login1">Website Username</label>
										<input class="pure-input-1-3" id="user_login" type="text" disabled="disabled" name="user_login" value="<?php the_author_meta( 'user_login', $current_user->ID ); ?>" />
									</div>
									<div class="pure-control-group"><label for="pass1">New Password</label>
										<input class="pure-input-1-3" id="pass1" style="height: 28px;" type="password" autocomplete="off" name="pass1" value="" />
									</div>
									<div class="pure-control-group">
										<label>Confirm Password</label>
										<input class="pure-input-1-3" id="pass2" style="height: 28px;" type="password" autocomplete="off" name="pass2" value="" />
									</div>
									<div class="pure-control-group"><label for="nickname">Nickname</label>
										<input class="pure-input-1-3" id="nickname" type="text" name="nickname" value="<?php the_author_meta( 'nickname', $current_user->ID ); ?>" /></div>
									<div class="pure-control-group"><label for="display_name">Display name publicly as</label>
										<select class="pure-input-1-3" id="display_name" name="display_name" current_display_name="<?php the_author_meta( 'display_name', $current_user->ID ); ?>">
											<option id="display_username"><?php the_author_meta( 'user_login', $current_user->ID ); ?></option>
											<option id="display_nickname"><?php the_author_meta( 'nickname', $current_user->ID ); ?></option>
											<option id="display_lastname"><?php the_author_meta( 'last_name', $current_user->ID ); ?></option>
											<option id="display_firstlast"><?php the_author_meta( 'first_name', $current_user->ID ); ?> <?php the_author_meta( 'last_name', $current_user->ID ); ?></option>
											<option id="display_lastfirst"><?php the_author_meta( 'last_name', $current_user->ID ); ?> <?php the_author_meta( 'first_name', $current_user->ID ); ?></option>
										</select>
									</div>
									<div class="pure-control-group">
										<label for="url">Personal Website</label>
										<input class="pure-input-1-2" id="url" style="width: 400px;" type="text" name="url" value="<?php the_author_meta( 'user_url', $current_user->ID ); ?>" />
									</div>
									<div class="pure-control-group">
										<label id="description-label" for="description">Personal Bio (displayed alongside proposed sessions)</label>
										<textarea style="width: 400px; font-size: 14px;" name="description" rows="6"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea>
									</div>

									<?php 
										//action hook for plugin and extra fields
										//do_action('edit_user_profile',$current_user); 
									?>
									<p class="form-submit">
										<?php echo $referer; ?>
										<input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
										<?php wp_nonce_field( 'update-user' ) ?>
										<input name="action" type="hidden" id="action" value="update-user" />
									</p><!-- .form-submit -->
								</form><!-- #adduser -->
							<?php endif; ?>
						</div><!-- .entry-content -->
					</article><!-- .hentry .post -->
					<?php endwhile; ?>
				<?php else: ?>
					<p class="no-data">
						<?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
					</p><!-- .no-data -->
				<?php endif; ?>
				
				
				</div><!-- #content .site-content -->
			</div><!-- #primary .content-area -->
		</div>
		<div class="five columns omega">
			<?php get_sidebar('Main Sidebar'); ?>
		</div>	
<?php get_footer(); ?>