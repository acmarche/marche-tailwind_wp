<?php


namespace AcMarche\MarcheTail\Inc;


use WP_Post;
use function add_action;
use function add_filter;
use const DOING_AUTOSAVE;

class Redirect
{
    public function __construct()
    {
        add_action('admin_menu', 'Redirect::title_link_meta');
        add_action("wp_insert_post", 'Redirect::title_link_save', 10, 2);
        add_filter('post_link', 'Redirect::filtre_title', 10, 2);
    }

    /**
     * Change url
     *
     * @param string $permalink
     *
     * @return string
     * @global WP_Post $post
     */
    static function filtre_title(string $permalink): string
    {
        global $post;
        $link = false;
        if (isset($post->ID)) :
            $id        = $post->ID;
            $meta_type = 'post';
            $meta_key  = 'acmarche_title_link';
            $single    = true;
            $link      = get_metadata($meta_type, $id, $meta_key, $single);

        endif;

        if ($link) {
            return $link;
        }

        return $permalink;
    }

    /**
     * to set a != link to post
     */
    static function title_link_meta()
    {
        add_meta_box(
            'title_link_data', // id of the metabox
            'Lien de l\'article', // Title
            'Redirect::title_link_content', //callback function that will "echo" the contents
            'post', // which post types will have it (page, post, link, wd_series)
            'side', //positioning: side, advanced
            'low' // positioning: low, high
        );
    }

    /**
     * render box content
     */
    static function title_link_content($post)
    {
        $link_data = get_post_custom($post->ID);
        $link      = '';
        if (isset($link_data['acmarche_title_link'])) {
            $link = $link_data['acmarche_title_link'][0];
        }
        ?>
        <div class="form-wrap">
            <div class="form-field form-required">
                <label for="acmarche_title_link">Lien de redirection de l'article</label>
                <input type="text" name="acmarche_title_link" value="<?php echo $link ?>"/>
                <p>Indiquez un lien pointant vers un autre article</p>
            </div>
        </div>
        <?php
    }

    static function title_link_save($post_id)
    {
        if (isset($_POST['acmarche_title_link'])) :

            // verify if this is an auto save routine.
            // If it is our form has not been submitted, so we dont want to do anything
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // verify this came from the our screen and with proper authorization,
            // because save_post can be triggered at other times
            // TO DO
            //   if (!wp_verify_nonce($_POST['myplugin_noncename'], plugin_basename(__FILE__)))
            //     return;
            // Check permissions
            if ('page' == $_POST['post_type']) {
                if ( ! current_user_can('edit_page', $post_id)) {
                    return;
                }
            } else {
                if ( ! current_user_can('edit_post', $post_id)) {
                    return;
                }
            }

            // OK, we're authenticated: we need to find and save the data

            $link = '';
            if (isset($_POST['acmarche_title_link'])) {
                $link = $_POST['acmarche_title_link'];
            }
            $old_link = get_post_meta($post_id, "acmarche_title_link", true);

            if ($link && $old_link != $link) {
                update_post_meta($post_id, "acmarche_title_link", $link);
            } elseif ( ! $link) {
                delete_post_meta($post_id, 'acmarche_title_link');
            }

        endif;
    }
}
