<?php
  

  $page_title = 'Lista de imagenes';
  require_once('include/load.php');
  /* comprobar que el usuario ha permitido que el nivel realice esta acción
   * nivel> = 2: usuarios privilegiados
   */
  page_require_level(2);

  /* datos requeridos */
  $media_files = find_all('media');
?>

<?php
  if (isset($_POST['submit'])) {
    
    if (isset($_POST['method']) && $_POST['method'] == "change_image") {
      if (!isset($_POST['media_id'])) {
        $session->msg('d', "Falta ID de imagen");
        redirect(SITE_URL.'media.php', false);     /* Como será recargada por Gio * 
        exit;
      }
      $id = $_POST['media_id'];
      if (!is_numeric($id) || ($id = intval($id)) < 1) {
        $session->msg('d', "ID de imagen incorrecto");
        redirect(SITE_URL.'media.php', false);     /* omo será recargada por Diego */
        exit;
      }

      $photo = new Media();

      if (!$photo->upload($_FILES['file_upload'])) {
        $session->msg('d',join(': ', $photo->errors) );
        redirect(SITE_URL.'media.php', false);
        exit;
      }
      if (!$photo->change_product_media($id)) {
        $session->msg('d',join(': ', $photo->errors) );
        redirect(SITE_URL.'media.php', false);
        exit;
      } else {
        $session->msg('s','Actualizado');
        redirect(SITE_URL.'media.php', false);
        exit;
      }
    }
    else {
      $photo = new Media();

      
      if (!$photo->upload($_FILES['file_upload'])) {
        $session->msg('d',join(': ', $photo->errors) );
        redirect(SITE_URL.'media.php', false);
      }

      if (!$photo->process_product_media()) {
        $session->msg('d',join(': ', $photo->errors) );
        redirect(SITE_URL.'media.php', false);
      } else {
        $session->msg('s','Imagen subida al servidor.');
        redirect(SITE_URL.'media.php', false);
      }
    }
  }
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-9">
    <?php echo display_msg($msg); ?>
  </div>

  <div class="col-md-9">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-camera"></span>
          <span>Lista de im&aacute;genes</span>
        </strong>
        <div class="pull-right">
          <form class="form-inline" action="media.php" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <div class="input-group">

            <!--ESTILO dinámico: usando jQuery para asignar el controlador de eventos
                   (usando jQuery para asignar el control de eventos ("click"))-->
              <span class="btn btn-success" id="search_file" style="margin-right:1pt" title="Seleccionar archivo" data-toogle="tooltip">Buscar
              </span>

              <!-- ESTILO antiguo: "codificado", especificando el atributo HTML "onclick"
                   (especificando el atributo HTML "onclick"-->
              <!--<div class="btn btn-success" id="search_file" style="margin-right:1pt" title="Seleccionar archivo" data-toogle="tooltip" onclick="document.getElementById('file_upload').click()">Buscar
              </div>-->
              
              <input type="file" name="file_upload" id="file_upload" style="display:none; visibility: hidden">
              <input type="hidden" name="method" id="method" value="">
              <input type="hidden" name="media_id" id="media_id" value="">
              <button type="submit" name="submit" id="submit" class="btn btn-primary"> Subir&nbsp; <span class="glyphicon glyphicon-open"></span></button>
           </div>
          </div>
         </form>
        </div>
      </div>
      <div class="panel-body">
        <table id="tbl-media" class="table table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;"></th>
              <th class="text-center">Imagen</th>
              <th class="text-center">Descripción</th>
              <th class="text-center" style="width: 20%;">Tipo</th>
              <th class="text-center" style="width: 10em;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($media_files as $media_file): ?>
            <tr class="list-inline">
              <td class="text-center" style="vertical-align: middle; font-size:150%"><?php echo count_id();?>
              </td>
              <td class="text-center">
                <img style="height: 6em; width: 6em;" data-toggle="tooltip" data-placement="right" title="doble click para cambiar" src="uploads/products/<?php echo $media_file['file_name'];?>" class="img-thumbnail" id="img_id_<?php echo (int) $media_file['id'];?>" />
              </td>
              <td class="text-center" style="vertical-align: middle; font-size:110%">
                <?php echo $media_file['file_name'];?>
              </td>
              <td class="text-center" style="vertical-align: middle; font-size:110%">
                <?php echo $media_file['file_type'];?>
              </td>
              <td class="text-center" style="vertical-align: middle">
                <div class="btn-group">
                  <a id="edit_media_id_<?php echo (int) $media_file['id'];?>" href="#" data-toggle="tooltip" data-placement="top" title="cambiar imagen">
                    <i class="glyphicon glyphicon-pencil"></i>
                  </a>
                  <a href="delete_media.php?id=<?php echo (int) $media_file['id'];?>" id="delete_media_<?php echo (int) $media_file['id'];?>" data-toggle="tooltip" data-placement="top" title="eliminar">
                    <i class="glyphicon glyphicon-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Este es el fondo de jQuery para esta página-->
<script type="text/javascript" src="lib/js/media.js"></script>

<!-- DataTable (solo para más de 10 elementos)-->
<?php if (count_id() > 10): ?>
<script type="text/javascript">
  $(document).ready(function() {
    $('#tbl-media').DataTable({
    })
  })
</script>
<?php endif; ?>

<?php include_once('layouts/footer.php'); ?>
