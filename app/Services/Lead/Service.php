<?php

namespace App\Services\Lead;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Models\AccountModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\UserModel;
use App\Models\Account;
use App\Models\Amouser;
use App\Models\Lead;
use App\Models\LeadTag;
use App\Models\Tag;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Service
{
    public function __construct(
        private readonly AmoCRMApiClient $api
    ) {}

    public function dump(): void
    {
        foreach ($this->getLeads() as $lead) {
            $this->extract($lead);
        }
    }

    private function dumpAmouser(UserModel $user): self
    {
        Amouser::firstOrCreate(
            $this->makeFields('amousers', $user->toArray())
        );
        return $this;
    }

    private function dumpAccount(AccountModel $account): self
    {

        if (!Account::where('id', '=', $account->getId())->exists()) {
            Account::create(
                $this->makeFields('accounts', $account->toArray())
            );
        }
        return $this;
    }

    public function dumpLead(LeadModel $lead): self
    {
        $fields = $this->makeFields('leads', $lead->toArray());

        (function () use ($lead, $fields): callable {
            $model = Lead::where('id', $lead->getId());
            if (!$model->exists()) {
                return fn() => Lead::create($fields);
            }
            return fn() => $model->update($fields);
        })()();

        return $this;
    }

    public function dumpLeadTags(LeadModel $lead, array $tags): self
    {
        foreach ($tags as $tag) {
            Tag::firstOrCreate($tag);
            LeadTag::create([
                'lead_id' => $lead->id,
                'tag_id' => $tag['id'],
            ]);
        }

        return $this;
    }

    private function extract($lead): void
    {

        $user = $this->getUser($lead->getResponsibleUserId());
        $account = $this->getAccount();
        $tags = $this->getTags($lead);

        try {
            DB::beginTransaction();

            $this->dumpAmouser($user)
                ->dumpAccount($account)
                ->dumpLead($lead)
                ->dumpLeadTags($lead, $tags);

            DB::commit();
        } catch (Exception $e) {
            dump($e);
            DB::rollBack();
        }
    }

    private function getLeads(): array
    {
        try {
            return $this->api->leads()->get()->all();
        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {
            dd($e);
        }
    }

    private function getUser(string $id): UserModel
    {
        try {
            return $this->api->users()->getOne($id);
        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {
            dd($e);
        }
    }

    private function getAccount(): AccountModel
    {
        try {
            return $this->api->account()->getCurrent();
        } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {
            dd($e);
        }
    }

    private function getTags($leads): array
    {
        if (!$leads->tags) {
            return array();
        }

        $tags = array();
        foreach ($leads->tags->toArray() as $tag) {
            $tags[] = $this->makeFields('tags', $tag);
        }

        return $tags;
    }

    private function makeFields(string $tableName, array $srcColumns): array
    {
        $tableColumns = array_flip(Schema::getColumnListing($tableName));
        $diff = array_diff_key($srcColumns, $tableColumns);
        return array_diff_key($srcColumns, $diff);
    }

}
