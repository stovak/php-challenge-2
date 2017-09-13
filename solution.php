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

/**
 * @param $pdo
 * @param $n
 *
 * @return array
 */
function dates_with_at_least_n_scores($pdo, $n) : array
{
   $toReturn = [];
   $stmt = $pdo->prepare("select date from scores where score = :n order by date DESC");
   $stmt->bindParam(":n", $n);
   $result = $stmt->execute();
   if ($result) {
      while($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
         array_push($toReturn, $record['date']);
      }
   }
   return $toReturn;
}

/**
 * @param $pdo
 * @param $date
 *
 * @return array
 */
function users_with_top_score_on_date($pdo, $date)
{
   $toReturn = [];
   $stmt = $pdo->prepare("select user_id from scores where date =  :date  and score = (select max(score) from scores where date = :date)");
   $stmt->bindParam(":date", $date);
   $result = $stmt->execute();
   if ($result) {
      while($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
         array_push($toReturn, $record['user_id']);
      }
   }
   return $toReturn;
}

/**
 * @param $pdo
 * @param $user_id
 * @param $n
 *
 * @return $array
 */
function dates_when_user_was_in_top_n($pdo, $user_id, $n)
{
   $toReturn = [];
   $stmt = $pdo->prepare("select date as INTOPDATE from scores where user_id = :user_id and score in (select distinct score from scores where user_id in (select user_id from scores where date = INTOPDATE order by score desc limit :n) and date = INTOPDATE) order by date desc");
   $stmt->bindParam(":user_id", $user_id);
   $stmt->bindParam(":n", $n);
   $results = $stmt->execute();
   if ($results) {
      $toReturn = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $toReturn = array_map(function($value){
         return $value['INTOPDATE'];
      }, $toReturn);
   }
   return $toReturn;
}
