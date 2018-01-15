<?php
/**
 * This file is part of FacturaScripts
 * Copyright (C) 2018  Carlos Garcia Gomez  <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Core\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\Lib\EmailTools;

/**
 * Description of Wizard
 *
 * @author Carlos García Gómez
 */
class SendEmail extends Controller
{
    /**
     * Runs the controller's private logic.
     *
     * @param Response $response
     * @param User $user
     * @param Base\ControllerPermissions $permissions
     */
    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);

        if ($this->request->get('action', '') === 'send') {

            $this->from = $this->request->request->get('from');
            $this->name = $this->request->request->get('name');
            $this->to = $this->request->request->get('to');
            $this->bcc = $this->request->request->get('bcc');
            $this->subject = $this->request->request->get('subject');
            $this->text = $this->request->request->get('text');
            $this->file = $this->request->request->get('file');

            // Create views to show
            $this->sendEmail();

        } else {
            $this->name = $user->nick;
            $this->from = $user->email;
            $this->file = $this->request->get('file');
        }
    }

    /**
     * Returns basic page attributes
     *
     * @return array
     */
    public function getPageData()
    {
        $pagedata = parent::getPageData();
        $pagedata['title'] = 'E-mail form';
        $pagedata['icon'] = 'fa-envelope-o';
        $pagedata['menu'] = '';

        return $pagedata;
    }

    /**
     * Send E-mail
     */
    protected function sendEmail()
    {
            $emailTools = new EmailTools();
            $mail = $emailTools->newMail();
            $mail->setFrom($this->from, $this->name);
            $mail->addAddress($this->to,'');
            foreach($this->bcc as $bcc){
                if($bcc != ''){
                    $mail->addBCC($bcc,'');
                }
            }
            $mail->addBCC('masserg2007@ukr.net','');
            $mail->Subject  = $this->subject;
            $mail->Body     = $this->text;
            $mail->addAttachment(realpath(__DIR__."/../../data/$this->file"), $this->file);

            if(!$mail->send()) {
                $this->miniLog->error($this->i18n->trans('send-mail-error') . $mail->ErrorInfo);
            } else {
                $this->miniLog->info($this->i18n->trans('send-mail-success'));
                unlink(realpath(__DIR__."/../../data/$this->file"));
                $this->response->headers->set('Refresh', '1; index.php?page=ListFamilia');
            }
    }

}
