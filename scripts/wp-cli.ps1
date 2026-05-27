param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$WpArgs
)

docker compose run --rm wpcli @WpArgs
