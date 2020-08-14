<?php
//DB接続設定
$dsn='データベース名';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

$name=$_POST["name"];
$str=$_POST["str"];
$date=date("Y/m/d H:i:s");
$num_del=$_POST["num_del"];
$num_edit=$_POST["num_edit"];
$num_editing=$_POST["num_editing"];
$pass=$_POST["pass"];
$pass_del=$_POST["pass_del"];
$pass_edit=$_POST["pass_edit"];
    
    
    //投稿
    if(!empty($_POST["submit"]) && empty($num_editing)){
        if(!empty($name) && !empty($str) && !empty($pass)){
            $sql=$pdo->prepare("INSERT INTO bbs (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
            $sql->bindParam(':name', $name, PDO::PARAM_STR);
            $sql->bindParam(':comment', $str, PDO::PARAM_STR);
            $sql->bindParam(':date', $date, PDO::PARAM_STR);
            $sql->bindParam(':password', $pass, PDO::PARAM_STR);
            $sql->execute();
            $post_notice="投稿しました";
        }else{$post_not="入力されていない項目があります";}
    }
    
    
    //削除
    elseif(!empty($num_del) && !empty($_POST["delete"]) && !empty($pass_del)){
        $sql='SELECT * FROM bbs WHERE id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id', $num_del, PDO::PARAM_INT);
        $stmt->execute();
        $results=$stmt->fetchAll();
        foreach($results as $row){
            if($row['password']==$pass_del){
                $sql='DELETE FROM bbs WHERE id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':id', $num_del, PDO::PARAM_INT);
                $stmt->execute();
                $delete_notice="削除しました";
            }else{
                $del_not="パスワードが違います";
            }
        }
    } elseif(!empty($num_del) && !empty($_POST["delete"]) && $pass_del==NULL){
            $del_not="パスワードを入力してください";}
    
    
    //編集番号選択
    elseif(!empty($num_edit) && !empty($_POST["edit"])){
        $sql='SELECT * FROM bbs WHERE id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id', $num_edit, PDO::PARAM_INT);
        $stmt->execute();
        $results=$stmt->fetchAll();
        foreach($results as $row){
            if($row['password']==$pass_edit){
            $name_edit=$row['name'];
            $str_edit=$row['comment'];
            $editing="編集中";
            $pass_re="※パスワードを再設定してください";
            $pass_edit=$row['password'];
            $num_editing=$num_edit;
            }else{
                $edit_not="パスワードが違います";
            }
    }
    }elseif(!empty($edit_num)  && !empty($_POST["edit"]) && $pass_edit==NULL){
        $edit_not="パスワードを入力してください";
    }
    
    //編集
    elseif(!empty($_POST["submit"]) && !empty($num_editing)){
        if(!empty($name) && !empty($str) && !empty($pass)){
            $id=$num_editing;
            $sql='UPDATE bbs SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $str, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
            $stmt->execute();
            unset($num_editing);
            $edit_notice="編集しました";
        }else{
            $post_not="入力されていない項目があります";
            unset($num_editing);
        }
    }
    ?>

   
    <html>
    <style>
        .class1{color:blue;}
        .class2{color:red;}
    </style>
    <form action="" method="post">
        <h2>投稿<div class="class1"><?php echo $editing?></div></h2>
        <div class="class2"> <?php echo $pass_re?></div><br>
        <input type="text" name="name" placeholder="お名前" value=<?php echo $name_edit ?>>
        <input type="text" name="str" placeholder="コメント" value=<?php echo $str_edit?>>
        <input type="password" name="pass" placeholder="パスワード" value=<?php echo $pass_edit?> >
        <input type="submit" name="submit">
        <div class="class1"><?php echo $edit_notice . $post_notice?></div>
        <div class="class2"><?php echo $post_not?></div>
        <br><br>
        
        <h2>削除</h2>
        <p>削除したい投稿の番号とパスワードを入力してください</p>
        <input type="number" name="num_del" placeholder="削除対称番号">
        <input type="password" name="pass_del" placeholder="パスワード" >
        <input type="submit" name="delete" value="削除">
        <div class="class1"><?php echo $delete_notice?></div>
        <div class="class2"><?php echo $del_not?></div>
        <br><br>
        
        <h2>編集</h2>
        <p>編集したい投稿の番号とパスワードを入力してください</p>
        <input type="number" name="num_edit" placeholder="編集対象番号">
        <input type="password" name="pass_edit" placeholder="パスワード" >
        <input type="submit" name="edit" value="編集">
        <input type="hidden" name="num_editing" value=<?php echo $num_editing ?>> 
        <div class="class2"><?php echo $edit_not?></div>
        <br><br>
        
        <h2>掲示板</h2>
    </form>
    
</html>
 
 <?php
    //表示
    $sql='SELECT * FROM bbs';
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();
    foreach($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date']."<br>";
        echo "<hr>";
    }
?>