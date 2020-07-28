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

    
     
    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    ?>

    
<div class="tabela_meta_box">
    <style>
      .tabela_meta_box table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
      }

      td,
      th {
        border: 1px solid #dddddd;
        text-align: center;
        padding: 8px;
      }

      .tabela_meta_box input[type=text] {
        width: 100%;
      }

      .botao_meta_box {
        padding: 10px 0;
      }

      .tabela_meta_box .none {
         display: none;
      }
    </style>
    <input type="text" class="none" name="meta_box_data" id="meta_box_data" value='<?php echo $titulo; ?>' />

    <table class="tabela_meta_box_table">
      <tr>
        <th style="width:5%">Ativar</th>
        <th style="width:45%">Titulo</th>
        <th style="width:45%">Link</th>
        <th style="width:5%"> - </th>
      </tr>
      <tr class="tabela_meta_box_linha" data-id="0">
        <td>
          <input type="checkbox" class="tabela_meta_box_input" />
        </td>
        <td>
          <input type="text" value="" class="tabela_meta_box_input" />
        </td>
        <td>
          <input type="text" value="" class="tabela_meta_box_input" />
        </td>
        <td>
          <button data-id="0" onclick="meta_box_function_excluir(this)" class="components-button is-secondary">Excluir</button>
        </td>
      </tr>
    </table>
    <div class="botao_meta_box">
      <button id="meta_box_btn_adicionar" class="components-button is-secondary">Adicionar</button>
      <button id="meta_box_btn_salvar" class="components-button is-primary">Salvar</button>
      <button id="meta_box_btn_montar" class="none">montar</button>
    </div>
  </div>

  <script>


    window.onload = meta_box_function_montar();

    document.querySelector("#meta_box_btn_adicionar").addEventListener('click', meta_box_function_adicionar);
    document.querySelector("#meta_box_btn_salvar").addEventListener('click', meta_box_function_salvar);
    document.querySelector("#meta_box_btn_montar").addEventListener('click',  meta_box_function_montar);

    function meta_box_function_montar() {
      console.log('aqui');
      var dados = document.querySelector("input#meta_box_data").value;

      var valores = dados ? JSON.parse(dados) : [];

      console.log(valores);

      valores.forEach((valor, index) => {


        var linha = document.querySelector(`tr[data-id="${index}"]`);
        console.log(linha);
        var inputs = linha.querySelectorAll("input.tabela_meta_box_input");


        console.log(inputs);

        inputs[0].checked = valor.ativo;
        inputs[1].value = valor.titulo;
        inputs[2].value = valor.link;

        meta_box_function_adicionar();

      });

    }

    function meta_box_function_salvar() {
      var linhas = document.querySelectorAll("tr.tabela_meta_box_linha");
      var dados = [];
      linhas.forEach(linha => {
        var inputs = linha.querySelectorAll("input.tabela_meta_box_input");
        if (inputs[1].value || inputs[2].value) {
          var dado = { ativo: inputs[0].checked, titulo: inputs[1].value, link: inputs[2].value };
          dados.push(dado);
        }
      });

      var final = JSON.stringify(dados);
      document.querySelector("input#meta_box_data").value = final;
    }

    function meta_box_function_excluir(e) {
      var id = e.getAttribute("data-id");
      document.querySelector(`tr[data-id="${id}"]`).remove();
    }

    function meta_box_function_adicionar() {
      let tabela = document.querySelector("table.tabela_meta_box_table");
      let referencias = tabela.querySelectorAll("tr.tabela_meta_box_linha");
      console.log(referencias.length);
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
   
  if( isset( $_POST['meta_box_data'] ) )
      update_post_meta( $post_id, 'meta_box_data', $_POST['meta_box_data']);
       
}


add_filter('the_content', 'xai_my_class');
function xai_my_class($content){
  global $post;
  $values = get_post_custom( $post->ID );

  $string = '';
  $linhas = '';
  

  
  if($values['meta_box_data'][0] && is_single()){

    $valores = json_decode($values['meta_box_data'][0]);
 
    $string = '<div style="widows: 100%; min-height: 100px; height: auto; padding: 15px 15px 30px 15px; background-color: #d9d9d9; margin: 60px 15px 75px 15px;"><h5 class="llms-h5 llms-lesson-title llms-lesson-preview" style="font-size: 22px;">Material para download</h5><ul style="margin-left: 15px;">';

    foreach($valores as $valor){
      if($valor->ativo){
        $linhas = $linhas . ' <li><a href="' . $valor->link . '" target="_blank">' . $valor->titulo . '</a></li>';
      }
     }
   
    $string = $string . $linhas. '</ul></div>';
   } 

    $content = $content . $string;
    return $content;
}
?>