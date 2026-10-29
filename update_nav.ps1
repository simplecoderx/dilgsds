# PowerShell script to replace Login nav item with LGRC and GAD dropdown in all HTML files
$old = '<li><a href="Login.html" class="nav-item" data-page="login"><span class="nav-label">Login</span></a></li>'
$new = '<li class="nav-item has-dropdown">
          <a href="LGRC-and-GAD.html" class="nav-link" data-page="lgrc-gad" aria-haspopup="true" aria-expanded="false">
            <span class="nav-label">LGRC and GAD</span>
            <span class="nav-arrow" aria-hidden="true">▾</span>
          </a>
          <ul class="nav-dropdown" aria-label="LGRC and GAD submenu">
            <li><a href="about-lgrc.html" class="dropdown-item">About LGRC</a></li>
            <li><a href="about-gad.html" class="dropdown-item">About GAD</a></li>
            <li><a href="gad-corner.html" class="dropdown-item">GAD Corner</a></li>
            <li><a href="msac.html" class="dropdown-item">MSAC</a></li>
          </ul>
        </li>'

$files = @(
    "index.html",
    "annual-report.html",
    "bac-advisory.html",
    "citizen-charter.html",
    "contact-us.html",
    "elected-officials.html",
    "foreign-travel-authority.html",
    "issuances.html",
    "joint-circulars.html",
    "legal-opinions-dilg-philippines.html",
    "legal-opinions.html",
    "memo-circulars.html",
    "mission-vision-values.html",
    "news.html",
    "organizational-structure.html",
    "presidential-directives.html",
    "process-flow-public-bidding.html",
    "programs-and-services.html",
    "quality-policy.html",
    "ra-10173.html",
    "republic-acts.html",
    "svp-checklist.html"
)

$basePath = "c:\xampp_new\htdocs\DILG Website1"
$count = 0

foreach ($file in $files) {
    $fullPath = Join-Path $basePath $file
    if (Test-Path $fullPath) {
        $content = Get-Content $fullPath -Raw
        if ($content.Contains($old)) {
            $content = $content.Replace($old, $new)
            Set-Content $fullPath $content -Force
            Write-Host "Updated: $file"
            $count++
        } else {
            Write-Host "SKIP (no Login nav found): $file"
        }
    } else {
        Write-Host "NOT FOUND: $file"
    }
}

Write-Host "`nDone! Updated $count files."

