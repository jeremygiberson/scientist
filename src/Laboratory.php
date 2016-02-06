<?php

namespace Scientist;

use Scientist\Journals\Journal;

/**
 * Class Laboratory
 *
 * The Laboratory is where the magic takes place. Here we define
 * and conduct our experiments.
 *
 * @package \Scientist
 */
class Laboratory
{
    /** @var bool  */
    protected $stopTrialsEarly = false;

    /**
     * Collection of journals to report to.
     *
     * @var array
     */
    protected $journals = [];

    /**
     * Experiments wont hide exceptions.
     * @return $this
     */
    public function stopTrialsEarly()
    {
        $this->stopTrialsEarly = true;
        return $this;
    }

    /**
     * Register a collection of journals.
     *
     * @param array $journals
     *
     * @return $this
     */
    public function setJournals(array $journals = [])
    {
        $this->journals = $journals;

        return $this;
    }

    /**
     * Register a new journal.
     *
     * @param \Scientist\Journals\Journal $journal
     *
     * @return $this
     */
    public function addJournal(Journal $journal)
    {
        $this->journals[] = $journal;

        return $this;
    }

    /**
     * Retrieve registers journals.
     *
     * @return array
     */
    public function getJournals()
    {
        return $this->journals;
    }

    /**
     * Start a new experiment.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function experiment($name)
    {
        return (new Experiment($name))->setLaboratory($this);
    }

    /**
     * Run an experiment.
     *
     * @param \Scientist\Experiment $experiment
     *
     * @return mixed
     */
    public function runExperiment(Experiment $experiment)
    {
        if ($experiment->shouldRun()) {
            $report = $this->getReport($experiment);
            return $report->getControl()->getValue();
        }

        return call_user_func_array(
            $experiment->getControl(),
            $experiment->getParams()
        );
    }

    /**
     * Run an experiment and return the result.
     *
     * @param \Scientist\Experiment $experiment
     *
     * @return \Scientist\Report
     */
    public function getReport(Experiment $experiment)
    {
        $intern = new Intern;
        if ($this->stopTrialsEarly) {
            $intern->overreact();
        }

        $report = $intern->run($experiment);
        $this->reportToJournals($experiment, $report);

        return $report;
    }

    /**
     * Report experiment result to registered journals.
     *
     * @param \Scientist\Experiment $experiment
     * @param \Scientist\Report     $report
     *
     * @return void
     */
    protected function reportToJournals(Experiment $experiment, Report $report)
    {
        foreach ($this->journals as $journal) {
            $journal->report($experiment, $report);
        }
    }
}
