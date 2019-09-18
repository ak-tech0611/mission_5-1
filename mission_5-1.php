<?php

	$filename="mission5"; //tbtest→mission5というテーブルに

	//データベース接続に必要な設定
	 $dsn='データベース名';
		$user='ユーザー名';
		$password='パスワード';
		$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

	//" "は機能をもったものを表示''は文字列としてしか認識しない


//---送信フォームの処理---
	if (!empty($_POST["send"])) { //送信ボタンに入力があったとき

		if (empty($_POST["name"])) { //nameに入力がなかったとき

			echo "名前を入力してください";

		}elseif (empty($_POST["comment"])) { //commentに入力がなかったとき

			echo "コメントを入力してください";

		}elseif (empty($_POST["send_pass"])) { //send_passに入力がなかったとき

			echo "パスワードを入力してください";

		}elseif (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["send_pass"])) { //3つとも入力されていたとき

		//----変数宣言----
			$id=1;	//変更する投稿番号
			$name=$_POST["name"];
			$comment=$_POST["comment"];
			$day=date("Y/m/d H:i:s");
			$send_pass=$_POST["send_pass"];
			$send=$_POST["send"];

			//---テーブルの作成(mission5というテーブル)---
			$sql = "CREATE TABLE IF NOT EXISTS $filename" //tableなかったらつくってね,tbtestが存在しないとき
				."("
				. "id INT AUTO_INCREMENT PRIMARY KEY," //数字型でAUTO_INCREMENT＝勝手に数字を増やしてくれる.PRIMARY KEY=id(番号)をメインにデータベースから探してきてね
				. "name char(32)," //char=文字数32ビット
				. "comment TEXT,"	//上限なしのテキスト型
				. "send_pass TEXT,"
				. "day DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
				.");";						//カラム3行作るよ
			$stmt = $pdo->query($sql); //pdoさんに仲介してもらってquery=問合せ(命令)
																//pdoによる命令と翻訳$sqlには命令文が入ってる


		//---送信されたものをデータベースに記入する---
			$sql = $pdo -> prepare("INSERT INTO $filename (name,comment,day,send_pass)VALUES('$name','$comment','$day','$send_pass')");	//準備
			$sql -> bindParam(":name",$name,PDO::PARAM_STR);	//PDOさんにまたパラメータはSTR（言葉ですよ）という
			$sql -> bindParam(":comment",$comment,PDO::PARAM_STR);	//パラメータを結びつける
			$sql -> bindParam(":day",$day,PDO::PARAM_INT);	//PDOさんにINT（数字ですよ）という
			$sql -> bindParam(":send_pass",$send_pass,PDO::PARAM_STR);
			$sql -> execute();	//実行

		}else{	//送信がおされたときに何も入力がされていないとき

			echo "何も入力されていません";

		}

	} //送信ボタン入力があったときの処理終わり
//---送信フォーム処理終わり---



//---送信後フォームの処理---
	if(!empty($_POST["edit_send"])) {	//送信ボタン(edit.ver)に入力があったとき
		if(empty($_POST["edit_name"])) {	//edit_nameに入力がなかったとき

			echo "編集後の名前を入力してください";

		}elseif (empty($_POST["edit_comment"])) {	//edit_commentに入力がなかったとき

			echo "編集後のコメントを入力してください";

		}elseif (empty($_POST["send_pass"])) {	//send_passに入力がなかったとき

			echo "編集後のパスワードを入力してください";

		}elseif (!empty($_POST["edit_name"]) && !empty($_POST["edit_comment"]) && !empty($_POST["edit_pass"]) && !empty($_POST["send_pass"]) && !empty($_POST["edit_num"])) { //edit_numはidとかぶる？

		//---変数宣言---
		$edit_name=$_POST["edit_name"];
		$edit_comment=$_POST["edit_comment"];
		$edit_num=$_POST["edit_num"];
		$edit_pass=$_POST["edit_pass"];
		$edit_day=date("Y/m/d H:i:s");

		$sql = "SELECT * FROM $filename";
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();

			foreach ($results as $row) {
				if ($row["id"] == $_POST["edit_num"] && $row["send_pass"] == $_POST["edit_pass"]) {

					//---編集作業---
					$sql = "update $filename set name=:name,comment=:comment,day=:day,send_pass=:send_pass where id=:id";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(":id",$edit_num,PDO::PARAM_INT);
					$stmt->bindParam(":name",$edit_name,PDO::PARAM_STR);
					$stmt->bindParam(":comment",$edit_comment,PDO::PARAM_STR);
					$stmt->bindParam(":day",$edit_day,PDO::PARAM_STR);
					$stmt->bindParam(":send_pass",$edit_pass,PDO::PARAM_STR);
					$stmt->execute();

				}
			}

		}else{

			echo "足りない情報があります。もう一度入力してください";

		}

	}
 //送信後フォームの処理終わり



//削除フォームの処理
	if (!empty($_POST["delete"])) { //削除ボタンが入力されたとき
		if (!empty($_POST["delete_num"])) { //delete_numに入力されているとき
			if (!empty($_POST["delete_pass"])) { //削除用パスワードの入力があったとき
			//---変数宣言---
				$delete_pass=$_POST["delete_pass"];
				$delete_num=$_POST["delete_num"];

					$sql = "SELECT * FROM $filename";
					$stmt = $pdo->query($sql);
					$results = $stmt->fetchAll();

						foreach ($results as $row) {
							if ($row["id"] == $_POST["delete_num"] && $row["send_pass"] == $_POST["delete_pass"]) {
								$id=$_POST["delete_num"];

								//---削除機能---
									$sql = "delete from $filename where id=:id";
									$stmt = $pdo->prepare($sql);
									$stmt->bindParam(":id",$id,PDO::PARAM_INT);
									$stmt->execute();

							}
						}

					echo $delete_num."番を削除しました";

			}else{	//delete_passに入力されていないとき

				echo "削除用パスワードを入力してください";

			}

		}else{	//delete_numに入力されていないとき

			echo "削除番号を入力してください";

		}
	}
//削除フォームの処理の終わり


//編集フォームの始まり
//編集フォームの処理
	if(!empty($_POST["edit"])) {	//追記
		if (!empty($_POST["edit_num"])) { //編集番号入力しているとき
			if (!empty($_POST["edit_pass"])) { //編集用パスワードが入力されているとき
				$edit_pass=$_POST["edit_pass"];
					$sql = "SELECT * FROM $filename"; //mission5テーブルを参照
					$stmt = $pdo->query($sql);
					$results = $stmt->fetchAll();

				foreach ($results as $a) {
					$edit_num=$_POST["edit_num"];

					if ($a["id"] == $edit_num && $a["send_pass"] == $edit_pass) { //passwordと$edit_passが一致したとき

								$edit_num=$a["id"];
								$edit_name=$a["name"];
								$edit_comment=$a["comment"];

						echo $edit_num."番の編集をします"."<br>";
						echo "名前とコメントとパスワード、編集番号及びパスワードを入力してください";

					}

				}
				unset($a);

			}else{

				echo "編集用パスワードの入力を入力してください";

			}

		}else{

			echo "編集番号の入力を入力してください";

		}

	}

//編集フォームの終わり

?>

<form action="mission_5-1.php" method="POST">

	<?php

		if (!empty($_POST["edit"])) { //編集ボタンに入力があったとき

					echo ("<input type='text' name='edit_name' value='$edit_name'>")."<br>";
					echo " "."<br>";
					echo ("<input type='text' name='edit_comment' value='$edit_comment'>")."<br>";
					echo ("<input type='hidden' name='edit_num' value='$edit_num'>");
					echo " "."<br>";
					echo ("<input type='password' name='send_pass' placeholder='パスワード入力欄'>")."<br>"; //追記
					echo " "."<br>";
					echo ("<input type='submit' name='edit_send' value='送信'>")."<br>"; //追記

		}else{

					echo ("<input type='text' name='name' placeholder='名前入力欄'>")."<br>";
					echo " "."<br>";
					echo ("<input type='text' name='comment' placeholder='コメント入力欄'>")."<br>";
					echo " "."<br>";
					echo ("<input type='password' name='send_pass' placeholder='パスワード入力欄'>")."<br>"; //追記
					echo " "."<br>";
					echo ("<input type='submit' name='send' value='送信'>")."<br>"; //追記

		}

	?>


	<br> <br>

	<p>	<input type="text" name="delete_num" placeholder="削除対象番号入力欄">	</p>
	<p>	<input type="password" name="delete_pass" placeholder="パスワード入力欄">	</p>
	<p>	<input type="submit" name="delete" value="削除">	</p>

	<br> <br>

	<p>	<input type="text" name="edit_num" placeholder="編集対象番号入力欄">	</p>
	<p>	<input type="password" name="edit_pass" placeholder="パスワード入力欄">	</p>
	<p>	<input type="submit" name="edit" value="編集">	</p>


</form>

<?php


//データベースに記入されたものを表示する
			$sql = "SELECT * FROM $filename";	//＊=カラム項目全部をとってくる nameだけにしたいなら＊をnameに変更する
			$stmt = $pdo->query($sql);	//実行する
			$results = $stmt->fetchAll();	//fetchは取得するという意味で今回はAllで全てを取得する

				foreach ($results as $row) {
						//$rowの中にはテーブルのカラム名が入る:[0][1][2]などでもよい
						echo $row["id"].',';
						echo $row["name"].',';
						echo $row["comment"].',';
						echo $row["day"].',';
						echo $row["send_pass"].'<br>';
				}


?>