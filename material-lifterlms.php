<?php
/*
Plugin Name: Material LifterLMS
Description: Esse plugin insere uma meta boxe na sidbar dentro do painel de cadastro das post "aulas (lesson)" disponíveis pelo plugin LifterLMS.
Author: Mizael Isaltino
*/

add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add()
{
    add_meta_box( 'material-lifterlms-meta-box-id', 'Material LifterLMS', 'cd_meta_box_cb', 'lesson', 'normal', 'default' );
}

function cd_meta_box_cb()
{
    // $post is already set, and contains an object: the WordPress post
    global $post;
    $values = get_post_custom( $post->ID );
    $titulo = isset( $values['meta_box_data'] ) ? $values['meta_box_data'][0] : '';
    // print_r(values);
    // die;
    
     
    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    ?>
    
    <input type="text" name="meta_box_data" id="meta_box_data" value='<?php echo $titulo; ?>' />
  <br>
  <table>
    <tr class="linha" data-id="0">
      <td>
        <label>Ativo</label>
        <input type="checkbox"  />
      </td>
      <td>
        <label>Titulo</label>
        <input type="text"  value="" />
      </td>
      <td>
        <label>Link</label>
        <input type="text"  value="" />
      </td>
      <td>
        <button data-id="1" onclick="excluir(this)">Excluir</button>
      </td>
    </tr>
  </table>
  <button onclick="adicionar()">Adicionar</button>
  <button onclick="salvar()">Salvar</button>
  <button onclick="montar()">montar</button>

  <script>

    window.onload = montar();

    function montar() {
      var dados = document.querySelector("#meta_box_data").value;
      //console.log(dados);

      var valores = dados ? JSON.parse(atob(dados)) : [];

      console.log(valores);

      valores.forEach((valor, index) => {
        //console.log(valores[index]);
        
        var linha = document.querySelector(`tr[data-id="${index}"]`);
        var inputs = linha.querySelectorAll("input");

        inputs[0].checked = valor.ativo;
        inputs[1].value = valor.titulo;
        inputs[2].value = valor.link;

          adicionar();

      });

    }

    function salvar() {
      var linhas = document.querySelectorAll(".linha");
      var dados = [];
      linhas.forEach(linha => {
        var inputs = linha.querySelectorAll("input");
        if (inputs[1].value || inputs[2].value) {
          var dado = { ativo: inputs[0].checked, titulo: inputs[1].value, link: inputs[2].value };
          dados.push(dado);
        }
      });

      var final = JSON.stringify(dados);
      console.log(btoa(final));
      document.querySelector("#meta_box_data").value = btoa(final);
    }

    function excluir(e) {
      var id = e.getAttribute("data-id");
      document.querySelector(`tr[data-id="${id}"]`).remove();
    }

    function adicionar() {
      let tabela = document.querySelector("table");
      let referencias = tabela.querySelectorAll("tr");
      let referencia = referencias[referencias.length - 1];
      var clone = referencia.cloneNode(true);
      var id = clone.getAttribute("data-id");

      clone.setAttribute("data-id", parseInt(id) + 1);
      var btn = clone.querySelector("button");
      var inputs = clone.querySelectorAll("input");

      inputs.forEach(input => {
        input.type != "checkbox" ? input.value = "" : input.checked = false;
      });

      btn.setAttribute("data-id", parseInt(id) + 1);
      tabela.appendChild(clone);
    }

  </script>
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
    $allowed = "";

    // print_r($_POST['meta_box_data']);
    // die;
      
  if( isset( $_POST['meta_box_data'] ) )
      update_post_meta( $post_id, 'meta_box_data', $_POST['meta_box_data']);
       
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
    <a href="' . $values['meta_box_link'][0] . '" target="_blank">' . $values['meta_box_data'][0] . '</a>
    </li>
    </ul>
    </div>'; 
  } 

    

    $content = $content . $string;
    return $content;
}
?>