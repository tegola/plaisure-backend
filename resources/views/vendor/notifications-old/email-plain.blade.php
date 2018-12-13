<?php

if (!empty($greeting)) {
    echo $greeting, "\n\n";
} else {
    echo $level == 'error' ? __('emails.common.greeting_error') : __('emails.common.greeting_success'), "\n\n";
}

if (!empty($introLines)) echo implode("\n", $introLines), "\n\n";

if (isset($actionText)) echo "{$actionText}: {$actionUrl}", "\n\n";

if (!empty($outroLines)) echo implode("\n", $outroLines), "\n\n";

if (!empty($salutation)) {
	echo $salutation, "\n"
} else {
	echo __('emails.common.salutation'), "\n";
	echo __('emails.common.salutation_name', ['name' => config('app.name')]), "\n";
}
