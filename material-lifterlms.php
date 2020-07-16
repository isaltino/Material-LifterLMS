<?php
/*
Plugin Name: Material LifterLMS
Description: Esse plugin insere uma meta boxe na sidbar dentro do painel de cadastro das post "aulas (lesson)" disponíveis pelo plugin LifterLMS.
Author: Mizael Isaltino
*/

add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add()
{
    add_meta_box( 'material-lifterlms-meta-box-id', 'Material LifterLMS', 'cd_meta_box_cb', 'lesson', 'side', 'default' );
}

function cd_meta_box_cb()
{
    // $post is already set, and contains an object: the WordPress post
    global $post;
    $values = get_post_custom( $post->ID );
    $link = isset( $values['meta_box_link'] ) ? $values['meta_box_link'][0] : '';
    $titulo = isset( $values['meta_box_titulo'] ) ? $values['meta_box_titulo'][0] : '';
    $check = isset( $values['meta_box_ativo'] ) ? esc_attr( $values['meta_box_ativo'][0] ) : '';
     
    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    ?>
    <div class="components-panel__row">
      <div class="components-base-control">
        <div class="components-base-control__field">
          </br>
          <input class="" type="checkbox" id="meta_box_ativo" name="meta_box_ativo" <?php checked( $check, true ); ?> />
          <label class="components-checkbox-control__label" for="meta_box_ativo">Ativo</label>
        </div>
      </div>
    </div>
    <div class="components-panel__row">
      <div class="components-base-control" style="width: 100%;">
        <div class="components-base-control__field">
          <label class="components-base-control__field" for="meta_box_titulo">Titulo</label>
          <input class="components-text-control__input"  type="text" name="meta_box_titulo" id="meta_box_titulo"
            value="<?php echo $titulo; ?>" />
        </div>
      </div>
    </div>

    <div class="components-panel__row">
      <div class="components-base-control" style="width: 100%;">
        <div class="components-base-control__field">
          <label class="components-base-control__field" for="meta_box_link">Link</label>
          <input class="components-text-control__input"  type="text" name="meta_box_link" id="meta_box_link"
            value="<?php echo $link; ?>" />
        </div>
      </div>
    </div>
     
    <?php    
}


add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id ){

 
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
     
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;

    // now we can actually save the data
    $allowed = array( 
      'a' => array( // on allow a tags
      'href' => array() // and those anchors can only have href attribute
      )
  );
   
  // Make sure your data is set before trying to save it
  if( isset( $_POST['meta_box_link'] ) )
      update_post_meta( $post_id, 'meta_box_link', wp_kses( $_POST['meta_box_link'], $allowed ) );

    if( isset( $_POST['meta_box_titulo'] ) )
      update_post_meta( $post_id, 'meta_box_titulo', wp_kses( $_POST['meta_box_titulo'], $allowed ) );
       
  // This is purely my personal preference for saving check-boxes
  $chk = isset( $_POST['meta_box_ativo'] ) && $_POST['meta_box_ativo'] ? true : false;
  update_post_meta( $post_id, 'meta_box_ativo', $chk );
}


add_filter('the_content', 'xai_my_class');
function xai_my_class($content){
  global $post;
  $values = get_post_custom( $post->ID );

  $string = '';

  // print_r($values['meta_box_ativo']);
  // die;
  
  if($values['meta_box_ativo'][0] && is_single()){

    $string = '<div style="widows: 100%; min-height: 100px; height: auto; padding: 15px 15px 30px 15px; background-color: #d9d9d9; margin: 60px 15px 75px 15px;">
    <h5 class="llms-h5 llms-lesson-title llms-lesson-preview" style="font-size: 22px;">Material para download</h5>
    <ul style="margin-left: 15px;">
    <li>
    <a href="' . $values['meta_box_link'][0] . '" target="_blank">' . $values['meta_box_titulo'][0] . '</a>
    </li>
    </ul>
    </div>'; 
  } 

    

    $content = $content . $string;
    return $content;
}
?>