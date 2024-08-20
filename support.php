<?php
//Получение адреса для отправки файла
function get_adress()
{
    $adress = '';
    return $adress;
}

/*----------------------------------------------------------------------------------------------------------------*/
//Стандартный код WP
    /*
    Template Name: support
    */
get_header(); ?>

	<!--<div id="primary" class="content-area">
		

		
		
			<div id="content" class="site-content" role="main">


			

			<?php /*if ( have_posts() ) : ?>

				

				<?php  ?>
				

				
				
				<?php while ( have_posts() ) : the_post(); ?>



					<?php
						get_template_part( 'content', get_post_format() );
					?>



				<?php endwhile; ?>


				<?php striker_content_nav( 'nav-below' ); ?>
				

				

			<?php else : ?>

				<?php get_template_part( 'no-results', 'index' ); ?>

			<?php endif; */?>

			</div>
			
			
			
			
		</div>#primary .content-area-->
		


<?php 
/*----------------------------------------------------------------------------------------------------------------*/
//Код разработанной формы

//Получение Адреса отправителя для доступа к странице
$user_ip = $_SERVER['REMOTE_ADDR'];
if ($user_ip == "46.146.206.74"){
  /*----------------------------------------------------------------------------------------------------------------*/

  //Если форма была отправлена
  if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    // Программная логика

    //Распределение данных с преобразованием
    $name = htmlspecialchars($_POST['PName']);
    $contact = htmlspecialchars($_POST['PContact']);
    $recipient = htmlspecialchars($_POST['PRecipient']);
    $problem = htmlspecialchars($_POST['PProblem']);
    $description = htmlspecialchars($_POST['PDescription']);

    //Формирование массива с данными для отправки на сервер
    $data = array(
      'name' => $name,
      'contact' => $contact,
      'problem' => $problem,
      'description' => $description
    );

    //Запись файлов в отдельнкю переменную
    $files = array();
    foreach($_FILES['PFiles']['error'] as $key => $value) {
      if ($_FILES['PFiles']['error'][$key]==0) {
        $filename = $_FILES['PFiles']['tmp_name'][$key];
        $text = file_get_contents($filename);		 
        $files[]=array(
        'name' => $_FILES['PFiles']['name'][$key],
        'size' => $_FILES['PFiles']['size'][$key],
        'data' => base64_encode($text)
        );
      }
    }
    //Если файлы были отправлены записапть в массив для отправки на сервер
    if (count($files)>0){
      $data['files']=$files;
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    //Отправка данных на сервер
    //Подготовка CURL
    $ch = curl_init(get_adress());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 	
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    //Отправка CURL с получением и расшифровкой результата
    $res = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $out = json_decode($res,true);

    if (curl_errno($ch)) { //Записать ошибку (при наличии)
      $error_msg = curl_error($ch);
    }

    curl_close($ch); //Освобождение ресурсов

    if (isset($error_msg)) { //Проверка на наличие ошибки в результате отправки
      //Вывести ошибку
      echo '<br><center><div class="mb-3">
              <div id="info" class="text h1"><b>'.$httpcode.' ошибка отправки</b></div>
            </div></center><br>';
    }else{
      //Вывести результат
      echo '<br><center><div class="mb-3">
              <div id="info" class="text h1"><b>'.$out['inf'].'</b></div>
            </div></center><br>';
    }
  } 


  //Формирование формы
	$form = 
    '<!doctype html>
    <html lang="ru">
    
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    
    <body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
      <div class="container text-center">
        <div class="row">
          <div class="col">
            <form action="" method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <div id="info" class="text h1">Как с вами связаться?</div>
              </div>
              <div class="mb-3">
                <div class="row">
                  <div class="col-sm-3">
                    <label for="exampleInputName" class="form-label"><h3>Как вас звать:</h3></label>
                  </div>
                  <div class="col">
                    <input style="font-size: large;" required name="PName" type="text" class="form-control" id="exampleInputName" placeholder="Ваше имя (ФИО)...">
                  </div>
                  <div class="col-sm-3"></div>
                </div>
              </div>
              <div class="mb-3">
                <div class="row">
                  <div class="col-sm-3">
                    <label for="exampleInputEmailTel" class="form-label"><h3>Эл. почта или телефон:</h3></label>
                  </div>
                  <div class="col">
                    <input style="font-size: large;" required name="PContact" type="text" class="form-control" id="exampleInputEmailTel" placeholder="Ваш адрес электронной почты или телефон...">
                  </div>
                  <div class="col-sm-3"></div>
                </div>
              </div>
    
              <!-- Telephone -->
    
              <div class="mb-3">
                <div id="info" class="text h1">С чем нужно помочь?</div>
              </div>
    
              <div class="mb-3">
                <div class="row">
                  <div class="col-sm-3">
                    <label for="exampleInputAdr1" class="form-label"><h3>Получатель:</h3></label>
                  </div>
                  <div class="col">
                    <!-- Получатель -->
                    <select style="font-size: large; width: 100%;" required name="PRecipient" id="exampleInputAdr1">
                      <option value="moodle">Moodle</option>
                      <option value="it">Тех. поддержка ЦИТ</option>
                      <option value="other">Прочее</option>
                    </select>
                  </div>
                  <div class="col-sm-3"></div>
                </div>
              </div> 
    
              <div class="mb-3">
                <div class="row">
                  <div class="col-sm-3">
                    <label for="exampleInputTel1" class="form-label"><h3>Тема:</h3></label>
                  </div>
                  <div class="col">
                    <input style="font-size: large;" required name="PProblem" type="text" class="form-control" id="exampleInputTem1" placeholder="Тема вашего обращения (кратко)...">
                  </div>
                  <div class="col-sm-3"></div>
                </div>
              </div>
              
              <div class="mb-3">
                <div class="row">
                  <div class="col-sm-3">
                    <label for="floatingTextarea2"><h3>Сообщение:</h3></label>
                  </div>
                  <div class="col">
                    <textarea style="font-size: large;" required name="PDescription" class="form-control" placeholder="Опишите проблему подробнее..." id="floatingTextarea2" style="height: 100px"></textarea>
                  </div>
                  <div class="col-sm-3"></div>
                </div>
              </div>
    
              <div class="mb-3">
                <div class="row">
                  <div class="col-sm-3">
                    <label for="formFileMultiple" class="form-label"><h3>Дополнительные файлы:</h3></label>
                  </div>
                  <div class="col">
                    <input style="font-size: large;" name="PFiles[]" class="form-control" type="file" id="formFileMultiple" multiple>
                  </div>
                  <div class="col-sm-3"></div>
                </div>
              </div>
              
              <button style="font-size: large;" type="submit" class="btn btn-primary">Отправить</button>
            </form>
          </div>
        </div>
      </div>
    </body>
    
    </html>';
    //Вывод формы на сайт
    echo $form;
}else{?>
	<H1>Доступ только из внутреней сети института</H1>
	
<?php
}
/*----------------------------------------------------------------------------------------------------------------*/
//Стандартный код WP
?>
<!-- footer -->
<?php get_footer(); ?>


