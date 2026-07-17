<?php
require_once '../../vendor/autoload.php';

if(!empty($_POST)){
    echo "Post OK";
}

// dump($_POST);

$title = 'Inscription';
?>

<form action="register.php" method="post" class="form-control m-auto mt-5" style="width: 50%;">
    <h2 class="text-center my-4">Inscription</h2>

    <div class="d-flex justify-content-between">
        <div>
            <label for="firstname" class="form-label">Prénom</label>
            <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Entrez votre prénom">
        </div>
        <div>
            <label for="lastname" class="form-label">Nom</label>
            <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Entrez votre nom">
        </div>
    </div><br>
    <div>
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Entrez votre email"><br>
        
        <label for="password" class="form-label">Mot de Passe</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Entrez votre mot de passe"><br>

        <label for="address" class="form-label">Addresse</label>
        <input type="text" name="address" id="address" class="form-control" placeholder="Entrez votre addresse">
    </div><br>
    
    <div class="d-flex justify-content-between">
        <div>
            <label for="phone" class="form-label">Téléphone</label>
            <input type="tel" name="phone" id="phone" class="form-control" placeholder="Entrez votre numéro">
        </div>
        <div>
            <label for="birth_date" class="form-label">Date de Naissance</label>
            <input type="date" name="birth_date" id="birth_date" class="form-control">
        </div>
    </div><br>

    <div>
        <input type="checkbox" name="remember_me" id="remember_me">
        <label for="remember_me">Se souvenir de moi</label>
    </div>
    <br>

    <div class="text-center">
        <span class="text-center"><p>Vous avez déjà un compte ? <a href="login.php" class="text-decoration-none">Se connecter</a></p></span> 
        <button type="submit" class="btn btn-success">Enregister</button>
    </div>
</form>