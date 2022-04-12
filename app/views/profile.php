<?php 
$this->layout('layout_id', ["title" => "Пользователь $username", "auth" => $auth, "id" => $id],); 
?>


 

<div class="subheader">
                <h1 class="subheader-title">
                    <i class='subheader-icon fal fa-user'></i> Профиль пользователя <?php echo $username; ?>
                </h1>
 </div>
             
            <div class="row">
              <div class="col-lg-6 col-xl-6 m-auto">

              <? 
        echo flash()->display();
         

        /**
         * Вы сообщения о роли пользовтеля
         */
        if ($auth->hasRole(\Delight\Auth\Role::ADMIN)) {
            echo '<h3> Вы Администратор </h3>';
        }
        /**
         * Если пользователь Admin, то ему открыт доступ для добавление и редактирования данных пользователей
         */
        if($canEditUsers == true){
            echo '<a class="btn btn-success"  href="/users">Список пользователей</a>';
            echo '<a class="btn btn-warning"  href="/create_user">Добавить пользователя</a>';
        }
        

                ?>

                    <!-- profile summary -->
                    <div class="card mb-g rounded-top">
                        <div class="row no-gutters row-grid">
                            <div class="col-12">
                                <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                <span class="status status-success mr-3">
                                <img src="http://alla.doodee.ru/img/demo/avatars/<?php echo $user['image'];?>" alt="" class="img-responsive" width="200">
                                </span>
                                    <h5 class="mb-0 fw-700 text-center mt-3">
                                    <?php echo $user['name']; ?>
                                        <small class="text-muted mb-0"> <?php echo $user['company']; ?></small>
                                    </h5>
                                    <div class="mt-4 text-center demo">
                                        <a href="javascript:void(0);" class="fs-xl" style="color:#C13584">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="fs-xl" style="color:#4680C2">
                                            <i class="fab fa-vk"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="fs-xl" style="color:#0088cc">
                                            <i class="fab fa-telegram"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 text-center">
                                    <a href="tel:<?php echo $user['phone']; ?>" class="mt-1 d-block fs-sm fw-400 text-dark">
                                        <i class="fas fa-mobile-alt text-muted mr-2"></i>  <?php echo $user['phone']; ?></a>
                                    <a href="mailto:<?php echo $email; ?>" class="mt-1 d-block fs-sm fw-400 text-dark">
                                        <i class="fas fa-mouse-pointer text-muted mr-2"></i> <?php echo $email; ?></a>
                                    <address class="fs-sm fw-400 mt-4 text-muted">
                                        <i class="fas fa-map-pin mr-2"></i>  <?php echo $user['adress']; ?>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
            </div> 

                 

                
               