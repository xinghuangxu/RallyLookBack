<?php

/**
 * @abstract A representation of Test Run Data Row in Test Run Table
 * @author Leonx
 * @copyright 2014 NetApp, Inc.
 * @version 1.0.0
 * 
 */

namespace Helper;

use Model\Task;

class StatusUpdate {

    private $tasks;

    public function __construct($ownername) {
        $this->tasks = Task::findWithOwnerName($ownername);
    }

    public function getTasksUpdatedToday() {
        $result = array();
        foreach ($this->tasks as $task) {
            if (date('Ymd') == date('Ymd', strtotime($task->LastUpdateDate) - 25200)) {
                $result[] = $task;
            }
        }
        return $result;
    }

    public function getTasksCompletedToday() {
        $result = array();
        foreach ($this->tasks as $task) {
            if (date('Ymd') == date('Ymd', strtotime($task->LastUpdateDate) - 25200) && $task->State == "Completed") {
                $result[] = $task;
            }
        }
        return $result;
    }

    public function getTasksBlocked() {
        $result = array();
        foreach ($this->tasks as $task) {
            if ($task->Blocked == "1") {
                $result[] = $task;
            }
        }
        return $result;
    }

    public function getTasksInProgress() {
        $result = array();
        foreach ($this->tasks as $task) {
            if ($task->State == "In-Progress") {
                $result[] = $task;
            }
        }
        return $result;
    }

    public function question($question) {
        return "<div class=\"question\">" . $question . "</div>";
    }

    public function answer($question) {
        return "<div class=\"answer\">" . $question . "</div>";
    }

    public function getHtmlReport() {
        $html = <<<CSS
            <style>
                .question {
                    margin-top: 25px;
                    font-weight: bold;
                }
            </style>
CSS;
        $completedHours = 0;
        //What you worked on today? (tasks udpated today)
        $tasks = $this->getTasksUpdatedToday();
        $html.=$this->question("What you worked on today?");
        foreach ($tasks as $task) {
            $html.=$task->toHtmlRow();
        }

        //How much progress you made? (Completed Task)
        $taskCompletedToday = $this->getTasksCompletedToday();
        $html.=$this->question("How much progress you made?");
        if (count($taskCompletedToday) > 0) {
            foreach ($taskCompletedToday as $task) {
                $html.=$task->toHtmlRow();
                $completedHours+=$task->Actuals;
            }
        } else {
            $html.="Not Much";
        }


        //Did you run into any road block? If so what? (Blocked Tasks)
        $taskBlocked = $this->getTasksBlocked();
        $html.=$this->question("Did you run into any road block? If so what?");
        if (count($taskBlocked) > 0) {
            $html.="Yes";
            foreach ($taskBlocked as $task) {
                $html.=$task->toHtmlRow();
                $html.="Block Reason: " . $task->BlockedReason;
            }
        } else {
            $html.="No";
        }
        //How many hours did you spend today? (Completed Tasks Hours)
        $html.=$this->question("How many hours did you spend today? $completedHours");

        //What are you planning to work on tomorrow? (In progress tasks)
        $tasksInProgress = $this->getTasksInProgress();
        $html.=$this->question("What are you planning to work on tomorrow?");
        foreach ($tasksInProgress as $task) {
            $html.=$task->toHtmlRow();
        }

        return $html;
    }

}
