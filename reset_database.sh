<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trust_wallet = trim($_POST['trust_wallet']);
    $onchain_wallet = trim($_POST['onchain_wallet']);
    
    $stmt = $pdo->prepare("UPDATE users SET trust_wallet = ?, onchain_wallet = ? WHERE id = ?");
    if($stmt->execute([$trust_wallet, $onchain_wallet, $user_id])) {
        $success = "Wallets mis à jour avec succès";
        
        // Rafraîchir les données
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } else {
        $error = "Erreur lors de la mise à jour";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="fade-in">
    <h1 style="margin-bottom: 2rem;">Configuration des wallets</h1>
    
    <div class="card">
        <?php if($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 1rem; border-radius: 10px; margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div style="text-align: center; padding: 2rem; background: linear-gradient(135deg, #f3f4f6, #ffffff); border-radius: 15px;">
                <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h3>Trust Wallet</h3>
                <p style="color: var(--gray); margin: 1rem 0;">Wallet mobile sécurisé pour crypto-monnaies</p>
                <?php if($user['trust_wallet']): ?>
                    <div style="background: var(--white); padding: 1rem; border-radius: 10px; word-break: break-all;">
                        <small>Adresse actuelle:</small><br>
                        <strong><?php echo $user['trust_wallet']; ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; padding: 2rem; background: linear-gradient(135deg, #f3f4f6, #ffffff); border-radius: 15px;">
                <i class="fas fa-link" style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h3>Onchain Wallet</h3>
                <p style="color: var(--gray); margin: 1rem 0;">Wallet décentralisé pour transactions on-chain</p>
                <?php if($user['onchain_wallet']): ?>
                    <div style="background: var(--white); padding: 1rem; border-radius: 10px; word-break: break-all;">
                        <small>Adresse actuelle:</small><br>
                        <strong><?php echo $user['onchain_wallet']; ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-shield-alt"></i> Adresse Trust Wallet</label>
                <input type="text" name="trust_wallet" class="form-control" 
                       value="<?php echo htmlspecialchars($user['trust_wallet']); ?>"
                       placeholder="Entrez votre adresse Trust Wallet">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-link"></i> Adresse Onchain Wallet</label>
                <input type="text" name="onchain_wallet" class="form-control" 
                       value="<?php echo htmlspecialchars($user['onchain_wallet']); ?>"
                       placeholder="Entrez votre adresse Onchain Wallet">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Mettre à jour les wallets
            </button>
        </form>
    </div>
    
    <div class="card">
        <h2><i class="fas fa-info-circle"></i> Information importante</h2>
        <ul style="margin-top: 1rem; list-style: none;">
            <li style="margin: 1rem 0;"><i class="fas fa-check-circle" style="color: var(--success);"></i> Vos adresses de wallet sont utilisées pour les dépôts et retraits</li>
            <li style="margin: 1rem 0;"><i class="fas fa-check-circle" style="color: var(--success);"></i> Assurez-vous que les adresses sont correctes avant de les sauvegarder</li>
            <li style="margin: 1rem 0;"><i class="fas fa-check-circle" style="color: var(--success);"></i> Vous pouvez configurer les deux wallets ou un seul selon vos besoins</li>
            <li style="margin: 1rem 0;"><i class="fas fa-check-circle" style="color: var(--success);"></i> Les retraits seront envoyés à l'adresse que vous avez configurée</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

