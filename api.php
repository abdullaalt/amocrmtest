<?php

    use AmoCRM\Client\AmoCRMApiClient;
    use Symfony\Component\Dotenv\Dotenv;

    include_once __DIR__ . '/vendor/autoload.php';

    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/bootstrap/.env.dist', __DIR__ . '/bootstrap/.env');

    include_once __DIR__ . '/bootstrap/token_actions.php';
    include_once __DIR__ . '/bootstrap/error_printer.php';

    use AmoCRM\Collections\ContactsCollection;
    use AmoCRM\Collections\LinksCollection;
    use AmoCRM\Exceptions\AmoCRMApiException;
    use AmoCRM\Filters\ContactsFilter;
    use AmoCRM\Filters\NotesFilter;
    use AmoCRM\Filters\EventsFilter;
    use AmoCRM\Filters\CustomFieldsFilter;
    use AmoCRM\Models\Factories\NoteFactory;
    use AmoCRM\Models\ContactModel;
    use AmoCRM\Models\NoteModel;
    use AmoCRM\Helpers\EntityTypesInterface;
    use AmoCRM\Collections\NotesCollection;
    use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
    use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
    use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
    use League\OAuth2\Client\Token\AccessTokenInterface;
    use AmoCRM\Models\NoteType\CommonNote;
    
    include_once __DIR__ . '/bootstrap/bootstrap.php';
abstract class Api{

    public $api = false;

    protected function auth(){
        $accessToken = getToken();

        $this->api = new AmoCRMApiClient($_ENV['CLIENT_ID'], $_ENV['CLIENT_SECRET'], $_ENV['CLIENT_REDIRECT_URI']);
    
        $this->api->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    saveToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                }
            );
    }
    
    public function getContactNotes($id){
        $leadNotesService = $this->api->notes(EntityTypesInterface::CONTACTS);
        $notesCollection = $leadNotesService->getByParentId($id, (new NotesFilter())->setNoteTypes([NoteFactory::NOTE_TYPE_CODE_COMMON]));

        return $notesCollection;
    }

    public function getContactById($id){
        return $this->api->contacts()->getOne($id)->toArray();
    }

    public function getCompaniesById($id){
        return $this->api->companies()->getOne($id)->toArray();
    }

    public function getContactLastServiceMessage($id){
        $leadNotesService = $this->api->notes(EntityTypesInterface::CONTACTS);
        $notesCollection = $leadNotesService->getByParentId($id);

        return $notesCollection;
    }

    public function getLeadEvents($ids, $created_at){

        $events = $this->api->leads()->getOne(1296313);

        $ev = $this->api->events();
        $events = $ev->get((new EventsFilter())
                            ->setEntity([EntityTypesInterface::LEADS])
                            ->setEntityIds([$ids])
                            ->setCreatedAt($created_at-10)
                            ->setLimit(100)
                        )->toArray();
        return $events;
    }

    public function getContactsFields($field_id){
        $fields = $this->api->customFields(EntityTypesInterface::CONTACTS)->getOne($field_id);

        return $fields;
    }

    public function getUserById($user_id){

        $user = $this->api->users()->getOne($user_id)->toArray();

        return $user;

    }

    public function addContactNotes($id, $message){
        $notesCollection = new NotesCollection();
        $serviceMessageNote = new CommonNote();

        $serviceMessageNote->setEntityId($id)
            ->setText($message);

        $notesCollection->add($serviceMessageNote);

        $leadNotesService = $this->api->notes(EntityTypesInterface::CONTACTS);
        $notesCollection = $leadNotesService->add($notesCollection);
    }

    public function addLeadNotes($id, $message){
        $notesCollection = new NotesCollection();
        $serviceMessageNote = new CommonNote();

        $serviceMessageNote->setEntityId($id)
            ->setText($message);

        $notesCollection->add($serviceMessageNote);

        $leadNotesService = $this->api->notes(EntityTypesInterface::LEADS);
        $notesCollection = $leadNotesService->add($notesCollection);
    }
    

}