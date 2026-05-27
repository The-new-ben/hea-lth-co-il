param(
    [string] $Domain = "hea-lth.co.il"
)

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RepoRoot = Split-Path -Parent $ScriptDir
$WorkspaceRoot = Split-Path -Parent $RepoRoot
$WpApi = Join-Path $WorkspaceRoot "tools\wp-api.ps1"

if (-not (Test-Path -LiteralPath $WpApi)) {
    throw "Cannot find wp-api helper at $WpApi"
}

$PageIds = @(
    674, 672, 670, 664, 662, 660, 652, 650, 648,
    646, 644, 612, 613, 614, 615, 610, 611, 2
)

$ChromeCss = @'
<style id="health-site-polish">
@import url('https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;700;800&display=swap');
:root{--health-font:"Heebo","Noto Sans Hebrew","Assistant",Arial,sans-serif;--health-ink:#102f38;--health-brand:#0d2f3a;--health-teal:#0f5964;--health-mint:#e8f7f3;--health-line:#d7e9e6}
body{background:#fbfdfc}
body #site-header.site-header{min-height:0!important;margin:0 0 26px!important;padding:10px 0!important;background:rgba(255,255,255,.98)!important;border-bottom:1px solid var(--health-line)!important;box-shadow:0 10px 30px rgba(13,47,58,.07)!important}
body #site-header .header-inner{width:min(1180px,calc(100% - 32px))!important;max-width:none!important;min-height:56px!important;margin:0 auto!important;display:flex!important;align-items:center!important;justify-content:space-between!important;gap:18px!important;direction:rtl!important;font-family:var(--health-font)!important}
body #site-header .site-branding{display:grid!important;grid-template-columns:42px minmax(0,1fr)!important;grid-template-rows:auto auto!important;align-items:center!important;justify-content:start!important;min-width:286px!important;text-align:right!important;column-gap:10px!important;row-gap:1px!important;direction:rtl!important}
body #site-header .site-logo,body #site-header .custom-logo-link,body #site-header img.custom-logo{display:none!important}
body #site-header .site-branding:before{content:"+";grid-row:1/3!important;grid-column:1!important;width:38px!important;height:38px!important;display:grid!important;place-items:center!important;border-radius:14px!important;background:linear-gradient(135deg,#0f766e,#119ab0)!important;color:#fff!important;font-family:Arial,sans-serif!important;font-size:27px!important;font-weight:800!important;line-height:1!important;box-shadow:0 8px 20px rgba(15,89,100,.18),inset 0 0 0 1px rgba(255,255,255,.34)!important}
body #site-header .site-branding:after{content:"\05E9\05D9\05E8\05D5\05EA\05D9\0020\05D1\05E8\05D9\05D0\05D5\05EA\0020\05E4\05E8\05D9\05DE\05D9\05D5\05DD";grid-column:2!important;grid-row:1!important;display:block!important;font-family:var(--health-font)!important;font-size:clamp(20px,2vw,26px)!important;font-weight:800!important;color:var(--health-brand)!important;letter-spacing:0!important;line-height:1.12!important;white-space:nowrap!important}
body #site-header .site-description{display:block!important;grid-column:2!important;grid-row:2!important;margin:0!important;padding:0!important;font-size:0!important;line-height:0!important}
body #site-header .site-description:before{content:"\05D4\05E9\05D5\05D5\05D0\05EA\0020\05E8\05D5\05E4\05D0\05D9\05DD,\0020\05E7\05DC\05D9\05E0\05D9\05E7\05D5\05EA\0020\05D5\05E9\05D9\05E8\05D5\05EA\05D9\05DD\0020\05E4\05E8\05D8\05D9\05D9\05DD";display:block!important;color:#0f766e!important;font-family:var(--health-font)!important;font-size:13px!important;font-weight:700!important;line-height:1.25!important;white-space:nowrap!important}
body #site-header .site-navigation-toggle-holder,body #site-header .site-navigation-dropdown{display:none!important}
body #site-header .site-navigation{display:flex!important;align-items:center!important;justify-content:flex-end!important;flex:1 1 auto!important;margin:0!important;min-width:0!important;font-family:var(--health-font)!important}
body #site-header .site-navigation .menu{display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:4px!important;list-style:none!important;margin:0!important;padding:0!important;flex-wrap:wrap!important}
body #site-header .site-navigation .menu li{margin:0!important;padding:0!important}
body #site-header .site-navigation .menu a{display:inline-flex!important;align-items:center!important;justify-content:center!important;min-height:36px!important;padding:8px 10px!important;border-radius:999px!important;color:#173b43!important;text-decoration:none!important;font-family:var(--health-font)!important;font-size:14px!important;font-weight:720!important;line-height:1.15!important;white-space:nowrap!important;letter-spacing:0!important}
body #site-header .site-navigation .menu a:hover,body #site-header .site-navigation .current-menu-item>a{background:var(--health-mint)!important;color:var(--health-teal)!important}
body #site-header .site-navigation .menu li:last-child>a{background:var(--health-teal)!important;color:#fff!important;border:1px solid var(--health-teal)!important;padding-inline:15px!important;box-shadow:0 8px 18px rgba(15,89,100,.18)!important}
body #content.site-main{width:min(1120px,calc(100% - 32px))!important;max-width:1120px!important;margin:20px auto 0!important;padding:0!important}
body #content .page-header{margin:0 0 22px!important;padding:0!important}
body #content h1.entry-title{font-family:var(--health-font)!important;font-size:clamp(30px,3.7vw,50px)!important;line-height:1.18!important;text-align:right!important;max-width:980px!important;margin:0 auto 18px!important;color:var(--health-ink)!important;font-weight:800!important;letter-spacing:0!important}
.pojo-a11y-toolbar-toggle{top:102px!important}
body #site-footer.site-footer{margin-top:58px!important;padding:40px 0 22px!important;background:#0d2f3a!important;color:#eafbf8!important;border-top:1px solid #c8e5df!important;font-family:var(--health-font)!important}
body #site-footer .footer-inner{width:min(1180px,calc(100% - 32px))!important;max-width:none!important;margin:0 auto!important;display:grid!important;grid-template-columns:minmax(260px,.85fr) minmax(0,2.15fr)!important;gap:28px!important;align-items:start!important;justify-items:stretch!important;direction:rtl!important}
body #site-footer .site-branding{display:grid!important;width:100%!important;grid-template-columns:46px minmax(0,1fr)!important;grid-template-rows:auto auto!important;text-align:right!important;margin:0!important;column-gap:12px!important;row-gap:4px!important;align-items:center!important;direction:rtl!important}
body #site-footer .site-logo,body #site-footer .custom-logo-link,body #site-footer img.custom-logo{display:none!important}
body #site-footer .site-branding:before{content:"+";grid-row:1/3!important;grid-column:1!important;width:42px!important;height:42px!important;display:grid!important;place-items:center!important;border-radius:15px!important;background:linear-gradient(135deg,#14b8a6,#0f766e)!important;color:#fff!important;font-family:Arial,sans-serif!important;font-size:30px!important;font-weight:800!important;line-height:1!important;box-shadow:0 10px 22px rgba(0,0,0,.18),inset 0 0 0 1px rgba(255,255,255,.28)!important}
body #site-footer .site-branding:after{content:"\05E9\05D9\05E8\05D5\05EA\05D9\0020\05D1\05E8\05D9\05D0\05D5\05EA\0020\05E4\05E8\05D9\05DE\05D9\05D5\05DD";grid-column:2!important;grid-row:1!important;display:block!important;font-family:var(--health-font)!important;font-size:25px!important;font-weight:800!important;color:#fff!important;line-height:1.16!important;letter-spacing:0!important}
body #site-footer .site-description{display:block!important;grid-column:2!important;grid-row:2!important;margin:0!important;padding:0!important;font-size:0!important;line-height:0!important}
body #site-footer .site-description:before{content:"\05D4\05E9\05D5\05D5\05D0\05D4\0020\05D5\05EA\05D9\05D0\05D5\05DD\0020\05E9\05DC\0020\05E8\05D5\05E4\05D0\05D9\05DD,\0020\05E7\05DC\05D9\05E0\05D9\05E7\05D5\05EA\0020\05D5\05E9\05D9\05E8\05D5\05EA\05D9\0020\05D1\05E8\05D9\05D0\05D5\05EA\0020\05E4\05E8\05D8\05D9\05D9\05DD.\0020\05DE\05D9\05D3\05E2\0020\05DB\05DC\05DC\05D9\0020\05D1\05DC\05D1\05D3\0020-\0020\05DC\05D0\0020\05D0\05D1\05D7\05D5\05DF\0020\05D5\05DC\05D0\0020\05D8\05D9\05E4\05D5\05DC.";display:block!important;max-width:340px!important;color:#cfe8e5!important;font-size:15px!important;font-weight:400!important;line-height:1.58!important}
body #site-footer .site-navigation{display:block!important;margin:0!important;width:100%!important}
body #site-footer .site-navigation:before{content:"\05DE\05E1\05DC\05D5\05DC\05D9\0020\05E9\05D9\05E8\05D5\05EA\0020\05D5\05D4\05E9\05D5\05D5\05D0\05D4";display:block!important;color:#fff!important;font-family:var(--health-font)!important;font-weight:820!important;font-size:17px!important;margin:0 0 12px!important;text-align:right!important}
body #site-footer .site-navigation .menu{display:grid!important;width:100%!important;grid-template-columns:repeat(4,minmax(0,1fr))!important;gap:9px!important;list-style:none!important;margin:0!important;padding:0!important}
body #site-footer .site-navigation .menu li{margin:0!important;padding:0!important;min-width:0!important}
body #site-footer .site-navigation .menu a{display:flex!important;align-items:center!important;justify-content:center!important;min-height:44px!important;padding:9px 10px!important;border-radius:12px!important;background:rgba(255,255,255,.065)!important;border:1px solid rgba(255,255,255,.12)!important;color:#f4fffd!important;text-decoration:none!important;font-family:var(--health-font)!important;font-size:13.5px!important;font-weight:650!important;line-height:1.28!important;text-align:center!important;letter-spacing:0!important}
body #site-footer .site-navigation .menu a:hover{background:rgba(255,255,255,.13)!important;color:#fff!important;border-color:rgba(255,255,255,.28)!important}
body #site-footer .copyright{grid-column:1/-1!important;margin:20px 0 0!important;padding-top:15px!important;border-top:1px solid rgba(255,255,255,.14)!important;color:#b9d9d5!important;font-size:13px!important;display:flex!important;justify-content:space-between!important;align-items:center!important;gap:12px!important;flex-wrap:wrap!important}
body #site-footer .copyright:before{content:"\05EA\05D5\05DB\05DF\0020\05DE\05EA\05E2\05D3\05DB\05DF\0020\05D5\05E0\05D1\05D3\05E7\0020\05DC\05E4\05D9\0020\05DE\05E7\05D5\05E8\05D5\05EA\0020\05E8\05E4\05D5\05D0\05D9\05D9\05DD\0020\05E6\05D9\05D1\05D5\05E8\05D9\05D9\05DD,\0020\05E2\05DD\0020\05D2\05D1\05D5\05DC\05D5\05EA\0020\05D1\05E8\05D5\05E8\05D9\05DD\0020\05DC\05DE\05E6\05D1\05D9\0020\05D7\05D9\05E8\05D5\05DD.";color:#d8f2ee!important}
body #site-footer .copyright p{margin:0!important;color:#b9d9d5!important}
.health-trust-band{direction:rtl;width:min(1120px,calc(100% - 32px));max-width:1120px;margin:44px auto 10px;padding:22px;border:1px solid #d9ebe8;border-radius:18px;background:#ffffff;box-shadow:0 12px 34px rgba(13,47,58,.08);display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;color:#173b43;font-family:var(--health-font)}
.health-trust-band strong{display:block;color:var(--health-brand);font-size:17px;font-weight:820;margin-bottom:6px}
.health-trust-band p{margin:0;color:#36545b;font-size:15px;line-height:1.65}
.health-trust-band a{color:var(--health-teal);font-weight:800;text-decoration:none}
.health-trust-band a:hover{text-decoration:underline}
body.page-id-2 .wp-block-button__link{font-family:var(--health-font)!important;border-radius:999px!important;font-weight:800!important;text-decoration:none!important;box-shadow:0 12px 26px rgba(15,89,100,.15)!important;border:1px solid rgba(15,89,100,.18)!important}
body.page-id-2 .wp-block-button:not(.is-style-outline) .wp-block-button__link{background:linear-gradient(135deg,#0f766e,#119ab0)!important;color:#fff!important}
body.page-id-2 .wp-block-button.is-style-outline .wp-block-button__link{background:#fff!important;color:#173b43!important;border:1px solid #173b43!important}
body.page-id-2 .wp-block-group.has-border-color{border-radius:18px!important;background:#fff!important;border-color:#d8e7e5!important;box-shadow:0 14px 32px rgba(13,47,58,.08)!important;transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease!important}
body.page-id-2 .wp-block-group.has-border-color:hover{transform:translateY(-2px)!important;box-shadow:0 18px 38px rgba(13,47,58,.11)!important;border-color:#b8ded8!important}
body.page-id-2 .wp-block-group.has-border-color h3{display:flex!important;align-items:center!important;gap:9px!important;margin-top:0!important;line-height:1.25!important}
body.page-id-2 .wp-block-group.has-border-color h3:before{content:"+";flex:0 0 34px!important;width:34px!important;height:34px!important;display:grid!important;place-items:center!important;border-radius:999px!important;background:#e4f7f3!important;color:#0f766e!important;font-family:Arial,sans-serif!important;font-size:24px!important;font-weight:800!important;box-shadow:inset 0 0 0 1px #c6e9e4!important}
body.page-id-2 .wp-block-group.has-border-color h3 a{color:#0d2f3a!important;text-decoration:none!important;font-weight:800!important}
body.page-id-2 .wp-block-group.has-border-color h3 a:hover{color:#0f766e!important}
@media(max-width:1020px){body #site-footer .footer-inner{display:block!important}body #site-footer .site-branding{width:min(100%,520px)!important;max-width:520px!important;margin:0 auto 24px!important;text-align:right!important}body #site-footer .site-navigation:before{text-align:center!important}body #site-footer .site-navigation{max-width:760px!important;margin-inline:auto!important}body #site-footer .site-navigation .menu{grid-template-columns:repeat(3,minmax(0,1fr))!important}}
@media(max-width:900px){body #site-header .header-inner{align-items:flex-start!important;flex-direction:column!important;gap:10px!important}body #site-header .site-branding{min-width:0!important;width:100%!important}body #site-header .site-navigation{width:100%!important;overflow-x:auto!important;justify-content:flex-start!important;padding-bottom:2px!important}body #site-header .site-navigation .menu{flex-wrap:nowrap!important;justify-content:flex-start!important;width:max-content!important;max-width:none!important}.health-trust-band{grid-template-columns:1fr;padding:18px;margin-top:34px}}
@media(max-width:680px){body #site-footer .site-navigation .menu{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:8px!important}body #site-footer .copyright{display:block!important;text-align:center!important}body #site-footer .copyright:before{display:block;margin-bottom:8px}}
@media(max-width:560px){body #site-header.site-header{padding:10px 0!important;margin-bottom:18px!important}body #site-header .site-branding{grid-template-columns:38px minmax(0,1fr)!important;column-gap:9px!important}body #site-header .site-branding:before{width:35px!important;height:35px!important;font-size:25px!important;border-radius:13px!important}body #site-header .site-branding:after{font-size:20px!important}body #site-header .site-description:before{font-size:12px!important;white-space:normal!important}body #content.site-main{width:min(100% - 24px,1120px)!important;margin-top:14px!important}body #content h1.entry-title{font-size:29px!important;line-height:1.2!important}body #site-footer.site-footer{padding-top:34px!important}body #site-footer .footer-inner{width:min(100% - 24px,1180px)!important}body #site-footer .site-branding{grid-template-columns:42px minmax(0,1fr)!important;max-width:330px!important}body #site-footer .site-branding:before{width:38px!important;height:38px!important;font-size:27px!important}body #site-footer .site-branding:after{font-size:22px!important}body #site-footer .site-description:before{font-size:14px!important;line-height:1.55!important}body #site-footer .site-navigation .menu a{font-size:12.5px!important;min-height:48px!important;padding:8px!important}}
</style>
'@

function Invoke-WPPostJson {
    param(
        [Parameter(Mandatory = $true)][string] $Route,
        [Parameter(Mandatory = $true)] $Payload
    )

    $temp = Join-Path $env:TEMP ("wp-body-" + [guid]::NewGuid().ToString() + ".json")
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($temp, ($Payload | ConvertTo-Json -Depth 50), $utf8NoBom)
    try {
        & $WpApi -Domain $Domain -Route $Route -Method POST -BodyPath $temp | Out-Null
    }
    finally {
        Remove-Item -LiteralPath $temp -Force -ErrorAction SilentlyContinue
    }
}

$Updated = @()

foreach ($PageId in $PageIds) {
    $page = & $WpApi -Domain $Domain -Route "/wp-json/wp/v2/pages/$PageId`?context=edit&_fields=id,slug,content"
    $raw = [string] $page.content.raw

    if ($raw -match '(?s)<style id="health-site-polish">.*?</style>') {
        $next = [regex]::Replace($raw, '(?s)<style id="health-site-polish">.*?</style>', [System.Text.RegularExpressions.MatchEvaluator]{ param($m) $ChromeCss }, 1)
    } else {
        $next = $ChromeCss + "`n" + $raw
    }


    if ($next -ne $raw) {
        Invoke-WPPostJson -Route "/wp-json/wp/v2/pages/$PageId" -Payload @{ content = $next }
        $Updated += [pscustomobject]@{ id = $PageId; slug = $page.slug; status = "updated" }
    } else {
        $Updated += [pscustomobject]@{ id = $PageId; slug = $page.slug; status = "unchanged" }
    }
}

$Updated | Format-Table -AutoSize
