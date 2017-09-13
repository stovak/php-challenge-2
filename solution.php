<?php
// YOUR NAME AND EMAIL GO HERE

/**
 * @param $request
 * @param $secret
 *
 * @return $payload|false
 */
function parse_request($request, $secret)
{
   list($receivedSignature, $payload) = explode(".", $request);
   $payload = base64_decode($payload);
   $receivedSignature = base64_decode($receivedSignature);
   $expectedSignature = hash_hmac('sha256', $payload, $secret);
   return (($expectedSignature == $receivedSignature) ? json_decode($payload, true) : false );
}

function dates_with_at_least_n_scores($pdo, $n)
{
    // YOUR CODE GOES HERE
}

function users_with_top_score_on_date($pdo, $date)
{
    // YOUR CODE GOES HERE
}

function dates_when_user_was_in_top_n($pdo, $user_id, $n)
{
    // YOUR CODE GOES HERE
}
