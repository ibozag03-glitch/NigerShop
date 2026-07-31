<?php
require_once dirname(__Dir__) . DIRECTORY_SEPARATOR . '../vendor/autoload.php';

$title = 'Connexion';
?>

<form action="" method="post" class="form-control m-auto mt-5" style="width: 50%;">
    <h2 class="text-center my-4">Connexion</h2>

    <div>
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Entrez votre email"><br>
        
        <label for="password" class="form-label">Mot de Passe</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Entrez votre mot de passe"><br>
    </div><br>

    <div>
        <input type="checkbox" name="remember_me" id="remember_me">
        <label for="remember_me">Se souvenir de moi</label>
        <a href="changeInfo.php" class="float-end">Mot de passe oublié ?</a>
    </div>
    <br>

    <div class="text-center">
        <span class="text-center"><p>Vous n'avez pas de compte ? <a href="register.php" class="text-decoration-none">S'Inscrire</a></p></span> 
        <button type="submit" class="btn btn-success">Enregister</button>
    </div>
</form>