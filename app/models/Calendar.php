<?php
/**
 * Modello Calendar per la gestione del calendario personale degli utenti
 */
class Calendar {
    private $conn;
    private $table = 'calendar_events';

    public $id;
    public $user_id;
    public $event_id;
    public $title;
    public $description;
    public $start_date;
    public $end_date;
    public $event_type;
    public $color;
    public $is_all_day;
    public $location;
    public $notification_minutes;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ottieni eventi del calendario per utente e mese
    public function getUserMonthEvents($user_id, $year, $month) {
        $start_date = "$year-$month-01";
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = :user_id 
                 AND (DATE(start_date) BETWEEN :start_date AND :end_date
                      OR DATE(end_date) BETWEEN :start_date AND :end_date
                      OR (DATE(start_date) <= :start_date AND DATE(end_date) >= :end_date))
                 ORDER BY start_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ottieni eventi di un giorno specifico
    public function getUserDayEvents($user_id, $date) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = :user_id 
                 AND (DATE(start_date) = :date OR 
                      (DATE(start_date) <= :date AND DATE(end_date) >= :date))
                 ORDER BY start_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Aggiungi evento al calendario
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 (user_id, event_id, title, description, start_date, end_date, 
                  event_type, color, is_all_day, location, notification_minutes) 
                 VALUES (:user_id, :event_id, :title, :description, :start_date, 
                         :end_date, :event_type, :color, :is_all_day, :location, :notification_minutes)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':event_id', $this->event_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':event_type', $this->event_type);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':is_all_day', $this->is_all_day);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':notification_minutes', $this->notification_minutes);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Aggiorna evento calendario
    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET title = :title, description = :description, 
                     start_date = :start_date, end_date = :end_date,
                     event_type = :event_type, color = :color, is_all_day = :is_all_day,
                     location = :location, notification_minutes = :notification_minutes,
                     updated_at = NOW()
                 WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':event_type', $this->event_type);
        $stmt->bindParam(':color', $this->color);
        $stmt->bindParam(':is_all_day', $this->is_all_day);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':notification_minutes', $this->notification_minutes);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        return $stmt->execute();
    }

    // Elimina evento calendario
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        return $stmt->execute();
    }

    // Ottieni evento per ID
    public function getById($id, $user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->event_id = $row['event_id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->event_type = $row['event_type'];
            $this->color = $row['color'];
            $this->is_all_day = $row['is_all_day'];
            $this->location = $row['location'];
            $this->notification_minutes = $row['notification_minutes'];
            return true;
        }
        return false;
    }

    // Aggiungi automaticamente eventi sportivi quando utente si iscrive
    public function addSportEventToCalendar($user_id, $event_id) {
        // Ottieni dettagli evento sportivo
        $eventQuery = "SELECT * FROM events WHERE event_id = :event_id";
        $stmt = $this->conn->prepare($eventQuery);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Crea evento calendario
            $this->user_id = $user_id;
            $this->event_id = $event_id;
            $this->title = $event['titolo'];
            $this->description = "Evento sportivo: " . $event['descrizione'];
            $this->start_date = $event['data_evento'];
            $this->end_date = null; // Evento sportivo tipicamente giornata intera
            $this->event_type = 'event';
            $this->color = '#10b981'; // Verde per eventi sportivi
            $this->is_all_day = true;
            $this->location = $event['luogo'];
            $this->notification_minutes = 60; // Notifica 1 ora prima
            
            return $this->create();
        }
        return false;
    }

    // Rimuovi evento sportivo dal calendario quando utente annulla iscrizione
    public function removeSportEventFromCalendar($user_id, $event_id) {
        $query = "DELETE FROM " . $this->table . " 
                 WHERE user_id = :user_id AND event_id = :event_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        
        return $stmt->execute();
    }

    // Ottieni eventi in arrivo (prossimi 7 giorni)
    public function getUpcomingEvents($user_id, $days = 7) {
        $start_date = date('Y-m-d H:i:s');
        $end_date = date('Y-m-d 23:59:59', strtotime("+$days days"));
        
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = :user_id 
                 AND start_date BETWEEN :start_date AND :end_date
                 ORDER BY start_date ASC
                 LIMIT 10";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Statistiche calendario
    public function getCalendarStats($user_id) {
        $stats = [];
        
        // Eventi questo mese
        $current_month = date('Y-m-01');
        $next_month = date('Y-m-01', strtotime('first day of next month'));
        
        $query = "SELECT 
                    COUNT(*) as total_events,
                    COUNT(CASE WHEN event_type = 'event' THEN 1 END) as sport_events,
                    COUNT(CASE WHEN event_type = 'training' THEN 1 END) as training_events,
                    COUNT(CASE WHEN event_type = 'personal' THEN 1 END) as personal_events
                 FROM " . $this->table . " 
                 WHERE user_id = :user_id 
                 AND start_date BETWEEN :start_date AND :end_date";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':start_date', $current_month);
        $stmt->bindParam(':end_date', $next_month);
        $stmt->execute();
        
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $stats;
    }

    // Genera dati per vista calendario (formato JSON per frontend)
    public function getCalendarData($user_id, $year, $month) {
        $events = $this->getUserMonthEvents($user_id, $year, $month);
        $calendarEvents = [];
        
        foreach ($events as $event) {
            $calendarEvents[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'start' => $event['start_date'],
                'end' => $event['end_date'],
                'allDay' => (bool)$event['is_all_day'],
                'backgroundColor' => $event['color'],
                'borderColor' => $event['color'],
                'extendedProps' => [
                    'description' => $event['description'],
                    'location' => $event['location'],
                    'type' => $event['event_type'],
                    'eventId' => $event['event_id']
                ]
            ];
        }
        
        return $calendarEvents;
    }
}
?>
