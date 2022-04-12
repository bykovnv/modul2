<?php 

$this->layout('layout_id', ['title' => 'Установить статус', "auth" => $auth, "id" => $id]); 

?>



<div class="subheader">
            <h1 class="subheader-title">
                <i class='subheader-icon fal fa-sun'></i> Установить статус у пользователя <?php echo $user['name'];?>
            </h1>

        </div>
        
        <form action="/status/edit" method="POST">
            <div class="row">
                <div class="col-xl-6">
                    <div id="panel-1" class="panel">
                        <div class="panel-container">
                            <div class="panel-hdr">
                                <h2>Установка текущего статуса</h2>
                            </div>
                            <div class="panel-content">
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- id -->
                                <div class="form-group">
                                    <input name="id" type="hidden" id="simpleinput" class="form-control" value="<?php echo $user['id']; ?>">
                                </div>
                                        <!-- status -->
                                        <div class="form-group">
                                        
                                            <label class="form-label" for="example-select">Выберите новый статус</label>
                                            <select class="form-control" id="example-select" name="status">
                                                <option value="1">Онлайн</option>
                                                <option value="2" >Отошел</option>
                                                <option value="3">Не беспокоить</option>
                                            </select>
                                        
                                        <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                        <button type="submit" class="btn btn-warning">Set Status</button>
                                    </div>
</div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
                 

                
               