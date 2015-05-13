<?php  

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

/**
 * Classe permettant de gérer les utilisateurs
 */
class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    /**
     * Vue permettant de créer un utilisateur
     */
    public function E013(){  
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->request->data['User']['PEMail'] == $this->request->data['User']['PEMailConf']) {
                if ($this->request->data['User']['PPassword'] == $this->request->data['User']['PPasswordConf']) {
                    $user['PSessionId'] = $this->Session->read('Config.userAgent');
                    foreach ($this->request->data['User'] as $key => $value) {
                        if (!($key == 'PTelephoneNum')) {
                            if ($key == 'PPassword' || $key == 'PPasswordConf') {
                                $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                                $user['PPassword'] = $passwordHasher->hash($value);
                            }
                            else if ($key == 'PEMail' || $key == 'PEMailConf') {
                                $user['PEMail'] = $value;
                            }
                            else {
                                $user[$key] = $value;
                            }
                        }
                    }
                    $user['PTelephoneNum'] = $this->request->data['User']['PTelephoneNum'];
                    $user['PPersonneId'] = null;
                }
            }
            $this->makeCall("Save_BAL_Vue_E013_Users", $user);
            $this->Session->setFlash('Utilisateur enregistré avec succès!', 'flash/success');
            // Envoi du mail pour activer son compte
            //ini_set('SMTP', 'smtp.gmail.com');
            //ini_set('sendmail_from', 'hubert.barret@gmail.com'); 
            //$message = "Bonjour,\nVeuillez activer votre compte pour pouvoir vous loguer prochainement: $this->Html->link('cliquez ici', array('controller'=>'users', 'action'=>'valideMail'))";
            //mail('hubert.barret@gmail.com', 'Activez votre compte FCPE', $message);
            $this->redirect(array('controller' => 'users', 'action' => 'E001'));
        }
    }

    /**
     * Vue permettant de loguer un utilisateur
     */
    public function E001() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $user_id = $this->Session->read('Auth.User.InterlocuteurId');
                $emailValide = $this->User->query("SELECT EMailValide FROM bal_interlocuteur WHERE InterlocuteurId=$user_id;");
                if ($emailValide[0]['bal_interlocuteur']['EMailValide']==0) {
                    $this->Session->setFlash('Veuillez confirmer votre compte.', 'flash/error');
                    $this->redirect($this->referer());
                }
                else {
                    $this->Session->setFlash('Vous êtes connecté!', 'flash/success');
                    $this->redirect(array('controller' => 'users', 'action' => 'asso'));
                }
            }
            else {
                $this->Session->setFlash('Erreur de password/identifiant!', 'flash/error');
            }
        }
    }

    public function logout() {
        $this->Auth->logout();
        $this->redirect(array('controller' => 'users', 'action' => 'e001'));
    }

    public function logged($conseilfcpeid=null) {
        $conseilfcpenom = $this->User->query("SELECT ConseiLFCPENom FROM bal_conseilfcpe WHERE ConseilFCPEId=$conseilfcpeid;");
        $user_id = $this->Session->read('Auth.User.InterlocuteurId');
        $habilitation = $this->User->query("SELECT HabilitationNom FROM bal_vue_e001_users WHERE ConseilFCPEId=$conseilfcpeid AND InterlocuteurId=$user_id");
        $etablissementid = $this->User->query("SELECT EtablissementId FROM bal_estassociea WHERE ConseilFCPEId=$conseilfcpeid;");
        $this->Session->write('Association.ConseilFCPEId', $conseilfcpeid);
        $this->Session->write('Association.ConseilFCPENom', $conseilfcpenom[0]['bal_conseilfcpe']['ConseilFCPENom']);
        $this->Session->write('User.HabilitationNom', $habilitation[0]['bal_vue_e001_users']['HabilitationNom']);
        $this->Session->write('Association.EtablissementId', $etablissementid[0]['bal_estassociea']['EtablissementId']);
        
        $this->Session->setFlash('Vous êtes connecté sur l\'association.', 'flash/success');
        $this->redirect('/');
    }

    /**
     * Vue permettant de choisir son association sur laquelle se loguer
     */
    public function asso() {
        $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
        $sessionid = $this->Session->read('Auth.User.EMail') . time();
        $sessionid = $passwordHasher->hash($sessionid);
        $this->Session->write('Auth.User.SessionId', $sessionid);

        $user_id = $this->Session->read('Auth.User.InterlocuteurId');
        $conseilfcpe = $this->User->query("SELECT ConseilFCPEId, ConseilFCPELabel FROM bal_vue_e001_assoc NATURAL JOIN bal_vue_e001_users WHERE InterlocuteurId=$user_id;");
        for ($i=0; $i < sizeof($conseilfcpe); $i++) { 
            $conseil[$i]['ConseilFCPEId'] = $conseilfcpe[$i]['bal_vue_e001_assoc']['ConseilFCPEId'];
            $conseil[$i]['ConseilFCPELabel'] = $conseilfcpe[$i]['bal_vue_e001_assoc']['ConseilFCPELabel'];
        }
        $this->set('conseil', $conseil);
    }

    /**
    *   Vue permettant la validation du mail d'un memblre de lassociation.
    */
    public function valideMail($mail = null){
        if(empty($mail)){
            $this->Session->setFlash('Email non valide.', 'flash/error');
            $this->redirect('/');
        }

        $this->makeCall('EMailConfirm', array($mail, true));
        $this->Session->setFlash('Email activé.', 'flash/success');
        $this->redirect('/');
    }

    /**
    *   Vue permettant la modification du password.
    */
    public function resetPassword(){
            if($this->request->is('post')){
                $mail = $this->request->data['resetMail']['mail'];
                $mdp = substr (md5($mail.time()), 0, 8);
                $crypt = $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                $mdpCrypt = $crypt->hash($mdp);
                //mail($mail, 'rez mdp', 'Nouveau mot de pase: '.$mdp);
                $this->makeCall('ResetPassword', array($mail, $mdpCrypt));
                $this->Session->setFlash('Password modifié, vous le receverez par mail.', 'flash/success');
                $this->redirect('/');
            }
        }


}


?>
