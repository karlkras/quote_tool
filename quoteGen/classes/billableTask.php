<?PHP

class lingo_billableTask {

    protected $id; // int
    protected $hourlyRate; //double
    protected $evenlyDistributed; //bool
    protected $name; // string
    protected $workRequired; // double
    protected $price; // double
    protected $projectID; // int
    protected $type; // string
    protected $markupPercent; // int
    protected $actualSellPricePer; // double
    protected $dbASPP;
    protected $overrideOK; // boolean
    protected $targLang; // string
    protected $sourceLang; // string
    protected $printable;

    function set_id($i) {
        $this->id = $i;
    }

    function get_id() {
        return $this->id;
    }

    function set_hourlyRate($h) {
        $this->hourlyRate = $h;
    }

    function get_hourlyRate() {
        return $this->hourlyRate;
    }

    function set_hourlyMinimum($f) {
        $this->hourlyMinimum = $f;
    }

    function get_hourlyMinimum() {
        return $this->hourlyMinimum;
    }

    function set_name($n) {
        $this->name = $n;
    }

    function get_name() {
        return $this->name;
    }

    function set_workRequired($p) {
        $this->workRequired = $p;
    }

    function get_workRequired() {
        return $this->workRequired;
    }

    function set_price($p) {
        $this->price = $p;
    }

    function get_price() {
        return $this->price;
    }

    function set_projectID($p) {
        $this->projectID = $p;
    }

    function get_projectID() {
        return $this->projectID;
    }

    function set_type($t) {
        $this->type = $t;
    }

    function get_type() {
        return $this->type;
    }

    function set_markupPercent($m) {
        $this->markupPercent = $m;
    }

    function get_markupPercent() {
        return $this->markupPercent;
    }

    function set_actualSellPricePer($a) {
        $this->actualSellPricePer = $a;
    }

//no getter since this is a special case. use aspp() function below.

    function set_overrideOK($bool) {
        $this->overrideOK = $bool;
    }

    function overrideOK() {
        return $this->overrideOK;
    }

    function set_targLang($l) {
        $this->targLang = $l;
    }

    function get_targLang() {
        return $this->targLang;
    }

    function set_sourceLang($l) {
        $this->sourceLang = $l;
    }

    function get_sourceLang() {
        return $this->sourceLang;
    }

    function set_evenlyDistributed($e) {
        $this->evenlyDistributed = $e;
    }

    function get_evenlyDistributed() {
        return $this->evenlyDistributed;
    }

    function set_printable($b) {
        $this->printable = $b;
    }

    function get_printable() {
        return $this->printable;
    }

    function set_dbASPP($a) {
        $this->dbASPP = $a;
    }

    function get_dbASPP() {
        return $this->dbASPP;
    }

    function cost() {
        return ($this->workRequired) * $this->hourlyRate;
    }

    function cspp() {
        if (($this->markupPercent == 0) || ($this->workRequired == 0)) {
            return 0;
        } else {
            return $this->cost() / (1 - ($this->markupPercent / 100)) / ($this->workRequired);
        }
    }

    function aspp() {
        if ($this->actualSellPricePer == -1) {
            //return ceil($this->cspp());
            return ceil(($this->hourlyRate) / (1 - ($this->markupPercent / 100)));
        } else {
            return $this->actualSellPricePer;
        }
    }

    function calcAspp() {
        return ceil(($this->hourlyRate) / (1 - ($this->markupPercent / 100)));
    }

    function asp() {
        return $this->aspp() * ($this->workRequired);
    }

    function gm() {
        if ($this->asp() == 0)
            return 0;
        else
            return (($this->asp() - $this->cost()) / $this->asp()) * 100;
    }

    function usesCustom() {
        if ($this->actualSellPricePer == -1) {
            return false;
        } else {
            return true;
        }
    }

}

?>