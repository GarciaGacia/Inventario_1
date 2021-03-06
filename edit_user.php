<?php
  $page_title = 'Editar Usuario';
  require_once('include/load.php');
  // Checkin 
  page_require_level(1);
?>
<?php
  $e_user = find_by_id('users',(int)$_GET['id']);
  $groups  = find_all('user_groups');
  if(!$e_user){
    $session->msg("d","Missing user id.");
    redirect(SITE_URL.'users.php');
  }
?>

<?php
  //Actualizar la información básica del usuario
  if (isset($_POST['update'])) {
    $req_fields = array('name','username','level');
    validate_fields($req_fields);
    if (empty($errors)) {
      $id = (int)$e_user['id'];
      $name = remove_junk($db->escape($_POST['name']));
      $username = remove_junk($db->escape($_POST['username']));
      $level = (int)$db->escape($_POST['level']);
      $status   = remove_junk($db->escape($_POST['status']));
      $sql = "UPDATE users SET name ='{$name}', username ='{$username}',user_level='{$level}',status='{$status}' WHERE id='{$db->escape($id)}'";
      $result = $db->query($sql);
      if ($result) {
        if ($db->affected_rows() === 1) {
          $session->msg('s',' Cuenta actualizada');
          redirect(SITE_URL.'users.php', false);
        } else {
          $session->msg('w',' No se cambió la información');
          redirect(SITE_URL.'edit_user.php?id='.(int)$e_user['id'], false);
        }
      } else {
        $session->msg('d',' Error en la transacción con la Base de Datos');
        redirect(SITE_URL.'edit_user.php?id='.(int)$e_user['id'], false);
      }
    } else {
      $session->msg("d", $errors);
      redirect(SITE_URL.'edit_user.php?id='.(int)$e_user['id'],false);
    }
  }
?>

<?php
// Actualizar contraseña de usuario
if (isset($_POST['update-pass'])) {
  $req_fields = array('password');
  validate_fields($req_fields);
  if (empty($errors)) {
    $id = (int)$e_user['id'];
    $password = remove_junk($db->escape($_POST['password']));
    $h_pass   = hash("sha512", $password);
    $sql = "UPDATE users SET password='{$h_pass}' WHERE id='{$db->escape($id)}'";
    $result = $db->query($sql);
    if ($result) {
      if ($db->affected_rows() === 1) {
        $session->msg('s',"Se ha actualizado la contraseña del usuario. ");
        redirect(SITE_URL.'edit_user.php?id='.(int)$e_user['id'], false);
      } else {
        $session->msg('w',' No se cambió la información');
        redirect(SITE_URL.'edit_user.php?id='.(int)$e_user['id'], false);
      }
    } else {
      $session->msg('d',' Error en la transacción con la Base de Datos');
      redirect(SITE_URL.'edit_user.php?id='.(int)$e_user['id'], false);
    }
  } else {
    $session->msg("d", $errors);
    redirect(SITE_URL.'edit_user.php?id='.(int)$e_user['id'],false);
  }
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12"> <?php echo display_msg($msg); ?> </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="glyphicon glyphicon-pencil mx-1"></i>
        Actualizar cuenta para: '<?php echo remove_junk(ucwords($e_user['name'])); ?>'
      </div>
      <div class="panel-body">
        <form method="post" action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" class="clearfix">
          <div class="form-group">
            <label for="name" class="control-label">Nombre Completo</label>
            <input type="name" class="form-control" name="name" value="<?php echo remove_junk(ucwords($e_user['name'])); ?>">
          </div>
          <div class="form-group">
            <label for="username" class="control-label">Username/Nick</label>
            <input type="text" class="form-control" name="username" value="<?php echo remove_junk($e_user['username']); ?>">
          </div>
          <div class="form-group">
            <label for="level">Rol de usuario</label>
            <select class="form-control" name="level">
              <?php foreach ($groups as $group ):?>
              <option <?php if($group['group_level'] === $e_user['user_level']) echo 'selected="selected"';?> value="<?php echo $group['group_level'];?>"><?php echo $group['group_name'];?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label for="status">Estado</label>
            <select class="form-control" name="status">
              <option <?php if($e_user['status'] === '1') echo 'selected="selected"';?>value="1">Activo</option>
              <option <?php if($e_user['status'] === '0') echo 'selected="selected"';?> value="0">Inactivo</option>
            </select>
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="update" class="btn btn-info">Actualizar</button>
          </div>
        </form>
      </div> 
    </div>
  </div>
  <!-- Formulario de cambio de contraseña -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <i class="fas fw fa-key mx-1"></i>
        Cambiar Password para: '<?php echo remove_junk(ucwords($e_user['name'])); ?>'
      </div>
      <div class="panel-body">
        <form action="edit_user.php?id=<?php echo (int)$e_user['id'];?>" method="post" class="clearfix">
          <div class="form-group">
                <label for="password" class="control-label">Contraseña</label>
                <input type="password" class="form-control" name="password" placeholder="Ingresa la nueva contraseña" required>
          </div>
          <div class="form-group clearfix">
            <button type="submit" name="update-pass" class="btn btn-danger">Cambiar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
