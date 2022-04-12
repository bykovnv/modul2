<?php 
use App\QueryBuilder;
$this->layout('layout_id', ["title" => "Загрузить аватар #id $id", "auth" => $auth, "id" => $id]); 
?>


<div class="subheader">
            <h1 class="subheader-title">
                <i class='subheader-icon fal fa-image'></i> Загрузить аватар пользователю <?php echo $user['name'];?>
            </h1>
        </div>
        <form action="/media/update" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-xl-6">
                    <div id="panel-1" class="panel">
                        <div class="panel-container">
                            <div class="panel-hdr">
                                <h2>Текущий аватар</h2>
                            </div>
                            <div class="panel-content">
                                <div class="form-group"> 
                                    <img src="http://alla.doodee.ru/img/demo/avatars/<?php echo $user['image'];?>" alt="" class="img-responsive" width="200">
                                </div>

                                <div class="form-group">
                                         <!-- id -->
                                    <input name="id" type="hidden" id="example-fileinput" class="form-control-file" value="<?php echo $user['id']; ?>" >
                                         <!-- image -->
                                    <label class="form-label" for="example-fileinput">Выберите аватар</label>
                                    <input name="image" type="file" id="example-fileinput" class="form-control-file">
                                </div>


                                <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                    <button class="btn btn-warning">Загрузить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
                

                
               