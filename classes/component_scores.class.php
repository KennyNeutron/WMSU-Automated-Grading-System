<?php

require_once 'database.php';

class ComponentScores
{
    public $score_id;
    public $items_id;
    public $grades_id;
    public $score;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function add($grades_id, $items_id, $score)
    {
        $sql = "INSERT INTO component_scores (items_id, grades_id, score) VALUES
        (:items_id, :grades_id, :score);";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':items_id', $items_id);
        $query->bindParam(':grades_id', $grades_id);
        $query->bindParam(':score', $score);

        return $query->execute();
    }

    function show()
    {
        $sql = "SELECT * FROM component_scores ORDER BY items_id ASC;";

        $query = $this->db->connect()->prepare($sql);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    public function calculateAverageByComponent($grades_id, $component_id)
    {
        $sql = "SELECT AVG(cs.score / c.component_quantity) * 100 as average 
                FROM component_scores cs
                INNER JOIN component_items c ON cs.items_id = c.items_id
                WHERE c.component_id = :component_id AND cs.grades_id = :grades_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':component_id', $component_id, PDO::PARAM_INT);
        $query->bindParam(':grades_id', $grades_id, PDO::PARAM_INT);

        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['average'] !== null ? round($result['average'], 2) : 0;
        }

        return 0;
    }
    public function calculateWeightByComponent($grades_id, $component_id)
    {
        $sql = "WITH ScorePercentage AS (
                    -- Calculate the percentage score per quiz
                    SELECT 
                        cs.grades_id,
                        ci.component_id,
                        cs.items_id,
                        cs.score,
                        ci.component_no,
                        ci.component_quantity,
                        (cs.score / ci.component_quantity) * 100 AS percentage_score
                    FROM component_scores cs
                    JOIN component_items ci ON cs.items_id = ci.items_id
                ),
                AveragePercentage AS (
                    -- Calculate the average percentage score of the student for the component
                    SELECT 
                        sp.grades_id,
                        sp.component_id,
                        AVG(sp.percentage_score) AS avg_percentage_score
                    FROM ScorePercentage sp
                    GROUP BY sp.grades_id, sp.component_id
                )
                -- Calculate the weighted score based on component weight
                SELECT 
                    ap.grades_id,
                    ap.component_id,
                    ap.avg_percentage_score,
                    c.weight,
                    (ap.avg_percentage_score / 100) * c.weight AS weighted_score
                FROM AveragePercentage ap
                JOIN sub_components c ON ap.component_id = c.component_id
                WHERE ap.grades_id = :grades_id
                AND ap.component_id = :component_id;";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':component_id', $component_id, PDO::PARAM_INT);
        $query->bindParam(':grades_id', $grades_id, PDO::PARAM_INT);

        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['weighted_score'] ?? 0;
        }

        return 0;
    }
    public function getScoreByItemStud($grades_id, $items_id)
    {
        $sql = "SELECT cs.score as score
                FROM component_scores cs
                INNER JOIN component_items c ON cs.items_id = c.items_id
                WHERE c.items_id = :items_id AND cs.grades_id = :grades_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':items_id', $items_id, PDO::PARAM_INT);
        $query->bindParam(':grades_id', $grades_id, PDO::PARAM_INT);

        if ($query->execute()) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['score'] !== null ? $result['score'] : 0;
        }

        return 0;
    }
    public function getAllScoreByItemStud($grades_id, $items_id)
    {
        $sql = "SELECT cs.score as score
                FROM component_scores cs
                INNER JOIN component_items c ON cs.items_id = c.items_id
                WHERE c.items_id = :items_id AND cs.grades_id = :grades_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':items_id', $items_id, PDO::PARAM_INT);
        $query->bindParam(':grades_id', $grades_id, PDO::PARAM_INT);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetchAll();
        }
        return $data;
    }
    function getScoreById($score_id)
    {
        $sql = "SELECT * FROM component_scores WHERE score_id = :score_id";

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':score_id', $score_id);
        $data = null;
        if ($query->execute()) {
            $data = $query->fetch();
        }
        return $data;
    }
    public function scoreExists($grades_id, $items_id)
    {
        $sql = "SELECT COUNT(*) FROM component_scores WHERE grades_id = ? AND items_id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$grades_id, $items_id]);
        return $stmt->fetchColumn() > 0;
    }
    public function updateScore($grades_id, $items_id, $score)
    {
        $sql = "UPDATE component_scores SET score = ? WHERE grades_id = ? AND items_id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute([$score, $grades_id, $items_id]);
    }
    public function delete($score_id)
    {
        $query = "DELETE FROM component_scores WHERE score_id = :score_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':score_id', $score_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>