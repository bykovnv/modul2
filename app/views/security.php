<?php 
use App\QueryBuilder;
$this->layout('layout_id', ['title' => 'Безопасность', "auth" => $auth, "id" => $id]); 
?>


<div class="subheader">
            <h1 class="subheader-title">
                <i class='subheader-icon fal fa-lock'></i> Безопасность
            </h1>
        <?php echo flash()->display(); ?>
        </div>
        <form action="/security/update" method="POST">
            <div class="row">
                <div class="col-xl-6">
                    <div id="panel-1" class="panel">
                        <div class="panel-container">
                            <div class="panel-hdr">
                                <h2>Обновление эл. адреса и пароля у пользователя <?php echo $user['name'];?></h2>
                            </div>
                            <div class="panel-content">
                                <!-- id hidden -->
                                <input type="hidden" name="id" id="simpleinput" class="form-control" value="<?php echo $user['id'];?>">
                                <!-- email -->
                                <div class="form-group">
                                    <label class="form-label" for="simpleinput">Email</label>
                                    <input type="email" name="email" id="simpleinput" class="form-control" value="<?php echo $user['email'];?>">
                                </div>

                                <!-- old password -->
                                <div class="form-group">
                                    <label class="form-label" for="simpleinput">Текущий пароль</label>
                                    <input type="password" name="oldPassword" id="simpleinput" class="form-control">
                                </div>

                                <!-- new password -->
                                <div class="form-group">
                                    <label class="form-label" for="simpleinput">Новый пароль</label>
                                    <input type="password" name="newPassword" id="simpleinput" class="form-control">
                                </div>


                                <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                    <button class="btn btn-warning">Изменить</button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>

                 

                
               