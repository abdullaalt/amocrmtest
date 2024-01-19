<?php
include_once(__DIR__.'/api.php');

final class MyAmo extends Api{

    private $data;
    public function __construct($request){

        $this->auth();

        if (isset($request['contacts'])){
            if (isset($request['contacts']['update'])){
                $this->data = $request['contacts']['update'][0];
                $this->contactUpdated();
            }else{
                $this->data = $request['contacts']['add'][0];
                $this->contactAdded();
            }
        }else{
            if (isset($request['leads']['update'])){
                $this->data = $request['leads']['update'][0];
                $this->leadUpdated();
            }else{
                $this->data = $request['leads']['add'][0];
                $this->leadAdded();
            }
        }
    }

    private function contactAdded(){
        
        $user = $this->getUserById($this->data['modified_user_id']);
        $message = $user['name'].' добавил контакт: '.$this->data['name'].'. ';
        $account = $this->getUserById($this->data['responsible_user_id']);
        $message .= 'Ответственный: '.$account['name'].'. ';
        $message .= 'Дата создания контакта: '.date('d.m.Y H:i', $this->data['date_create']);

        $this->addContactNotes($this->data['id'], $message);

    }

    private function contactUpdated(){
        
        $user = $this->getUserById($this->data['modified_user_id']);
        $message = $user['name'].' изменил следующие поля: ';
        $fields = [];
        foreach ($this->data['custom_fields'] as $d){
            $fields[] = $d['name'].' - '.$d['values'][0]['value'];
        }

        $message .= implode(',', $fields).'. ';
        $message .= 'Дата изменения контакта: '.date('d.m.Y H:i', $this->data['last_modified']);

        $this->addContactNotes($this->data['id'], $message);
    }

    private function leadAdded(){
        
        $user = $this->getUserById($this->data['modified_user_id']);
        $message = $user['name'].' добавил сделку: '.$this->data['name'].'. ';
        $account = $this->getUserById($this->data['responsible_user_id']);
        $message .= 'Ответственный: '.$account['name'].'. ';
        $message .= 'Дата создания сделки: '.date('d.m.Y H:i', $this->data['date_create']);

        $this->addLeadNotes($this->data['id'], $message);

    }

    private function leadUpdated(){
        
        $user = $this->getUserById($this->data['modified_user_id']);
        $message = $user['name'].' изменил следующие поля: ';
        $fields = [];
        $events = $this->getLeadEvents($this->data['id'], $this->data['updated_at']);
        foreach ($events as $event){

            if ($event['type'] == 'entity_linked'){
                if ($event['value_after'][0]['link']['entity']['type'] == 'contact'){
                    $contact = $this->getContactById($event['value_after'][0]['link']['entity']['id']);
                    $fields[] = 'добавил контакт - '.$contact['name'];
                }

                if ($event['value_after'][0]['link']['entity']['type'] == 'company'){
                    $contact = $this->getCompaniesById($event['value_after'][0]['link']['entity']['id']);
                    $fields[] = 'добавил компанию - '.$contact['name'];
                }
            }

            if ($event['type'] == 'entity_unlinked'){
                if ($event['value_before'][0]['link']['entity']['type'] == 'contact'){
                    $contact = $this->getContactById($event['value_before'][0]['link']['entity']['id']);
                    $fields[] = 'открепил контакт - '.$contact['name'];
                }

                if ($event['value_before'][0]['link']['entity']['type'] == 'company'){
                    $contact = $this->getCompaniesById($event['value_before'][0]['link']['entity']['id']);
                    $fields[] = 'открепил компанию - '.$contact['name'];
                }
            }

            if ($event['type'] == 'name_field_changed'){
                $fields[] = 'изменил имя на - '.$event['value_after'][0]['name_field_value']['name'];
            }

            if ($event['type'] == 'sale_field_changed'){
                $fields[] = 'изменил бюджет на - '.$event['value_after'][0]['sale_field_value']['sale'];
            }

        }

        $message .= implode('; ', $fields);
        $message .= '. Дата изменения сделки: '.date('d.m.Y H:i', $this->data['last_modified']);

        $this->addLeadNotes($this->data['id'], $message);
    }

}

$myAmo = new MyAmo($_REQUEST);