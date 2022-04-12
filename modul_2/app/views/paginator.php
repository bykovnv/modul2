<?php

$this->layout('layout', ["title" => "Пагинация",  "posts" => $posts,  ]); 

?>

        
            <div class="subheader">
                <h1 class="subheader-title">
                    <i class='subheader-icon fal fa-users'></i> Список пользователей
                </h1>

                

            </div>
          
            

<div class="row" id="js-contacts">
 
        
     <?php foreach($posts as $post) {
   echo $post['id'] . ' ' . $post['title'] . "<br>";
   
     } 

     ?>

 
 </div>

  
 
</div>
                 

                
               