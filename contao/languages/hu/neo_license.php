<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licencek';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Licenckulcs', 'A rendelés visszaigazolásában szereplő kulcs. A mező kiürítése és mentése eltávolítja a licencet erről a weboldalról.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Ellenőrzés most';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Ellenőrzés …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Utoljára ellenőrizve: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'A licencszolgáltatás a legutóbbi ellenőrzéskor nem volt elérhető. Új próbálkozás automatikusan egy órán belül vagy az „Ellenőrzés most” gombbal.';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'A licenc nem terjed ki erre: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'A licenckulcs a rendelés visszaigazolásában található. Egy licenc eltávolításához erről a weboldalról ürítse ki a mezőjét, és mentsen.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Licenckulcs vásárlása az áruházban';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Magasabb kiadás vagy megújítás az áruházban';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => '%1$s licenc, érvényes eddig: %2$s, domain: %3$s.',
    'ok_ending' => '%1$s licenc felmondva, érvényes eddig: %2$s, domain: %3$s.',
    'ok_grace' => '%1$s licenc lejárt ekkor: %2$s. Megújítás nélkül eddig érvényes: %4$s.',
    'ok_unlimited' => '%1$s licenc, korlátlan ideig érvényes, domain: %3$s.',
    'no_key' => 'Nincs licenc.',
    'invalid_key' => 'Ez a licenckulcs nem érvényes ehhez a bővítményhez.',
    'no_certificate' => 'A licenckulcs tárolva van, de még nincs megerősítve.',
    'unverifiable' => 'A licencszolgáltatás tanúsítványa a bővítmény ezen verziójával nem ellenőrizhető.',
    'wrong_module' => 'A tanúsítvány egy másik bővítményhez tartozik.',
    'domain' => 'A licenc nem vonatkozik erre a domainre. Licencelt: %s.',
    'expired' => 'A licenc lejárt.',
    'revoked' => 'A licencet visszavonták.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'érvényes';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Ez a domain nem szerepel a licencben. Vegye fel az ügyfélterületen, vagy rendeljen további domaineket.',
    'installationsExhausted' => 'A licenc összes telepítése foglalt.',
    'domainsPerInstallationExceeded' => 'Ez a telepítés több domaint használ, mint amennyit a licenc engedélyez. Rendeljen további domaineket telepítésenként.',
    'other' => 'a licenc nem fedi le',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Telepítések: %2$d közül %1$d foglalt';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Nem aktiválható: a licenc telepítéseinek száma túllépve. A licenc már minden engedélyezett telepítésben aktív (alapértelmezés szerint egyben). A telepítések számának növeléséhez forduljon ide: %s.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['legfeljebb %d domain telepítésenként', 'legfeljebb %d domain telepítésenként'];
