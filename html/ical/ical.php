<?php
require_once "../../utils.php";
if (isset($_GET["token"])) {

    $token = $_GET['token'];
    $scope = request("SELECT date_debut, date_fin FROM sae._ical_token WHERE token='$token'",1);
    
    if(!empty($scope)){
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');
    
        print "BEGIN:VCALENDAR\n";
        print "VERSION:2.0\n";
        print "PRODID:-//AlhaizBreizh//Icalator//EN\n";
        $logements = request("SELECT titre, l.id FROM sae._ical_token_logements t INNER JOIN sae._logement l ON t.logement = l.id WHERE token='$token'");
        foreach($logements as $logement){
            $id_logement = $logement["id"];
            $titre = $logement["titre"];
            $sql = "SELECT date, statut FROM sae._calendrier c WHERE c.date >= '" . $scope['date_debut'];
            $sql .= "' AND c.date <= '" . $scope['date_fin'];
            $sql .= "' AND id_logement = $id_logement";
            $status = request($sql);

            $sql = "SELECT pays, region, departement, commune, numero, nom_voie FROM sae._adresse a";
            $sql .= " INNER JOIN sae._logement l ON a.id = l.id_adresse";
            $sql .= " WHERE l.id = $id_logement";
            $adresse = join(" ", request($sql, 1));

            foreach($status as $s){
                $nom;
                $status_event = "CONFIRMED";
                $desc = "Votre logement \"$titre\", situé à l'adresse $adresse est réservé ce jour-ci. \n";
                switch ($s["statut"]){
                    case 'R':
                        $nom = "Réservation";
                        break;
                    case 'D':
                        $nom = "Devis en cours";
                        $status_event = "TENTATIVE";
                        $desc = "Votre logement \"$titre\", situé à l'adresse $adresse à un devis en cours ce jour-ci. \n";
                        break;
                    case 'I':
                        $nom = "Indisponible";
                        $desc = "Votre logement \"$titre\", situé à l'adresse $adresse est indisponible ce jour-ci. \n";
                        break;
                }
                print "BEGIN:VEVENT\n";
                print "DTSTART:" . gmdate('Ymd', strtotime($s['date'])) . "\n";
                print "DTEND:" . gmdate('Ymd', strtotime($s['date'])) . "\n";
                print "SUMMARY:" . $nom.": ". $titre . "\n";
                print "LOCATION:".$adresse."\n";
                print "STATUS:".$status_event."\n";
                print "DESCRIPTION:$desc";
                print "END:VEVENT\n";
            }
            
        }
        print "END:VCALENDAR";
    }
}
?>