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
            if (date('Ymd') == date('Ymd', strtotime($task->LastUpdateDate) - 25200)&& $task->State != "Defined") {
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
        return "<div style=\"margin-top: 25px;font-weight: bold;\" class=\"question\">" . $question . "</div>";
    }

    public function answer($answer) {
        return "<table style=\"margin-left: 10px;\" class=\"answer\">" . $answer . "</table>";
    }

    public function getHtmlReport() {
        $html = "";
        $completedHours = 0;
        //What you worked on today? (tasks udpated today)
        $tasks = $this->getTasksUpdatedToday();
        $html.=$this->question("What you worked on today?");
        $answer="";
        foreach ($tasks as $task) {
            $answer.=$task->toHtmlRow();
        }
        $html.= $this->answer($answer);
        
        
        //How much progress you made? (Completed Task)
        $taskCompletedToday = $this->getTasksCompletedToday();
        $html.=$this->question("How much progress you made?");
        $answer="";
        if (count($taskCompletedToday) > 0) {
            foreach ($taskCompletedToday as $task) {
                $answer.=$task->toHtmlRow();
                $completedHours+=$task->Actuals;
            }
        } else {
            $answer.="<tr>Not Much<tr>";
        }
         $html.= $this->answer($answer);

        //Did you run into any road block? If so what? (Blocked Tasks)
        $taskBlocked = $this->getTasksBlocked();
        $html.=$this->question("Did you run into any road block? If so what?");
        $answer="";
        if (count($taskBlocked) > 0) {
            $answer.="<tr>Yes</tr>";
            foreach ($taskBlocked as $task) {
                $answer.=$task->toHtmlRow();
                $answer.="<tr><td>Block Reason:</td> <td>" . $task->BlockedReason."</td></tr>";
            }
        } else {
            $answer.="<tr>No</tr>";
        }
        $html.= $this->answer($answer);
        
        
        //How many hours did you spend today? (Completed Tasks Hours)
        $html.=$this->question("How many hours did you spend today?");
        $html.= $this->answer($completedHours);
        
        //What are you planning to work on tomorrow? (In progress tasks)
        $tasksInProgress = $this->getTasksInProgress();
        $html.=$this->question("What are you planning to work on tomorrow?");
        $answer="";
        foreach ($tasksInProgress as $task) {
            $answer.=$task->toHtmlRow();
        }
        $html.= $this->answer($answer);
        return $html;
    }

}
