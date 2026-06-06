<?php

namespace app\Application\Panel;

use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Application\Panel\Contract\AssistantDesignStorageInterface;
use app\Application\Panel\Contract\AssistantParamsRepositoryInterface;
use app\Application\Panel\Dto\DesignViewData;
use app\Application\Panel\Dto\ParamsViewData;
use app\Domain\Client\ClientModuleAccess;
use app\Presentation\Http\Form\Designe;

final class ManageAssistantSettingsService
{
    public function __construct(
        private readonly AssistantParamsRepositoryInterface $params,
        private readonly AssistantDesignStorageInterface $designStorage,
        private readonly ClientModuleAccessRepositoryInterface $moduleAccess,
    ) {
    }

    public function getDesignViewData(int $publicKey): DesignViewData
    {
        $this->designStorage->ensureFiles($publicKey);

        $designe = new Designe();
        $designe->CustomCss = $this->designStorage->getCustomCss($publicKey);
        $designe->LogoSvg = $this->designStorage->getLogoSvg($publicKey);

        return new DesignViewData(
            $this->params->findOrCreateForClient($publicKey),
            $designe,
        );
    }

    public function saveDesign(int $publicKey, array $post): bool
    {
        $this->designStorage->ensureFiles($publicKey);

        $designe = new Designe();
        $designe->load($post);

        if (!$designe->validate()) {
            return false;
        }

        if (isset($post['Designe']['CustomCss'])) {
            $this->designStorage->saveCustomCss($publicKey, (string)$post['Designe']['CustomCss']);
        }

        if (isset($post['Designe']['LogoSvg'])) {
            $this->designStorage->saveLogoSvg($publicKey, (string)$post['Designe']['LogoSvg']);
        }

        return $this->params->saveFromPost(
            $this->params->findOrCreateForClient($publicKey),
            $post,
            $publicKey,
        );
    }

    public function getParamsViewData(int $publicKey): ParamsViewData
    {
        $this->designStorage->ensureFiles($publicKey);
        $params = $this->params->findOrCreateForClient($publicKey);

        return new ParamsViewData(
            $params,
            $this->buildConnectionCode($publicKey),
            $this->moduleAccess->getForClient($publicKey),
        );
    }

    public function saveParams(int $publicKey, array $post): bool
    {
        $this->designStorage->ensureFiles($publicKey);
        $moduleAccess = $this->moduleAccess->getForClient($publicKey);

        return $this->params->saveFromPost(
            $this->params->findOrCreateForClient($publicKey),
            $this->filterParamsPostByModuleAccess($post, $moduleAccess),
            $publicKey,
        );
    }

    private function filterParamsPostByModuleAccess(array $post, ClientModuleAccess $moduleAccess): array
    {
        if (!isset($post['Params']) || !is_array($post['Params'])) {
            return $post;
        }

        if (!$moduleAccess->allows('chatbots')) {
            unset($post['Params']['default_answer']);
        }

        if (!$moduleAccess->allows('bigdata')) {
            unset(
                $post['Params']['chatbot_bigdata_system_id'],
                $post['Params']['chatbot_bigdata_is_active'],
            );
        }

        return $post;
    }

    private function buildConnectionCode(int $publicKey): string
    {
        if (!($_ENV['ISINFOCOD'] ?? false)) {
            return '';
        }

        $domain = $_ENV['DOMAININFOAPIWIDGET'];
        $domainStatic = $_ENV['DOMAININFOSTATICWIDGET'];
        $domainCustom = $_ENV['DOMAININFOCUSTOMWIDGET'];
        $domainWs = $_ENV['DOMAININFOWSWIDGET'] ?? '';

        return "&lt;script&gt;
    window.Smartius = {
        apiUrl: '" . $domain . "/api',
        staticUrl: '" . $domainStatic . "',
        customUrl: '" . $domainCustom . "',
        supportWsUrl: '" . $domainWs . "',
        publicKey: " . $publicKey . ",
        _user: {
            id: null,
            role: null,
            name: null,
            email: null
        }
    };
    var script = document.createElement('script');
    script.src = '$domainStatic/lib.js', document.head.appendChild(script);
&lt;/script&gt;";
    }
}
