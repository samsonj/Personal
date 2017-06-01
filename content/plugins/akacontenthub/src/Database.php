<?php

namespace ContentHub;

/*
Database class --> bridge between feed item objects and database
*/

class Database {

    public $tablename;
    public $wpdbObj;

	public function __construct() {
        global $wpdb;
        $this->wpdbObj = $wpdb;
        $this->tablename = $this->wpdbObj->prefix."contenthub_items";
    }

    public function getFeedItem($id)
    {
        $query = $this->wpdbObj->get_row("SELECT * FROM {$this->tablename} WHERE id={$id};");
        return $query;
    }

    public function insertFeedItem($feedItem)
    {
        $insertArray = array(
            'type' => $feedItem->type,
            'description' => stripslashes($feedItem->description),
            'username' => stripslashes($feedItem->username),
            'date' => $feedItem->date,
            'image' => $feedItem->image,
            'link' => $feedItem->link
        );
        if($feedItem->css_classes) {
            $insertArray['css_classes'] = $feedItem->css_classes;
        }
        if(
            $this->wpdbObj->insert(
                $this->tablename,
                $insertArray
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function updateFeedItem($feedItem)
    {
        $this->wpdbObj->update(
            $this->tablename,
            array(
                'type' => $feedItem->type,
                'description' => stripslashes($feedItem->description),
                'username' => $feedItem->username,
                'date' => $feedItem->date,
                'image' => $feedItem->image,
                'link' => $feedItem->link,
                'css_classes' => $feedItem->css_classes
            ),
            array(
                'id' => $feedItem->id
            )
        );
        return true;
    }

    public function removeFeedItem($idFeedItem)
    {
        if(
            $this->wpdbObj->delete(
                $this->tablename,
                array(
                    'id' => $idFeedItem
                ),
                array( '%d' )
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function updateFeedItemOrder($idFeedItem, $menuOrder) {
        if(
            $this->wpdbObj->update(
                $this->tablename,
                array(
                    'menu_order' => $menuOrder
                ),
                array(
                    'id' => $idFeedItem
                ),
                array( '%d' ),
                array( '%d' )
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function get_contenthub_feed($limit = -1, $offset = -1 , $feedtype = "" , $fromdate = "" ) {
        $limit   = filter_var($limit, FILTER_VALIDATE_INT);
        $offset  = filter_var($offset, FILTER_VALIDATE_INT);


        $limitOffset = '';
        if($limit > 0 && $offset > -1) {
            $limitOffset = "LIMIT $limit OFFSET $offset";
        }

        // Add a where clause
        $where = "";

        if(!empty($feedtype)) {
            if(is_array($feedtype)) {
                $counter = 0;
                foreach($feedtype as $type) {
                    $type    = filter_var($type, FILTER_SANITIZE_STRING);
                    if($counter == 0) {
                        $where .= "WHERE (type = '$type' ";
                    } else {
                        $where .= "OR type = '$type' ";
                    }
                    $counter++;
                }
                $where .= ")";
            } elseif(is_string($feedtype)) {
                $feedtype    = filter_var($feedtype, FILTER_SANITIZE_STRING);
                $where .= "WHERE type = '$feedtype' ";
            }
            // from date
            if(!empty($fromdate)) {
                $where .= "AND date >= '$fromdate' ";
            }
        } else {
            if(!empty($fromdate)) {
                $where .= "WHERE date >= '$fromdate' ";
            }
        }

        $sql = "SELECT * FROM ".$this->wpdbObj->prefix."contenthub_items $where ORDER BY menu_order ASC, id DESC $limitOffset;" ;
        return $this->wpdbObj->get_results($sql);
    }

    public function count_contenthub_feed_items($feedtype = "")
    {
        $where = " ";
        if(is_array($feedtype)) {
            $feedtype        = filter_var_array($feedtype, FILTER_SANITIZE_STRING);
            $feedtype_string = implode("',  '", array_map($this->make_type_selector, $feedtype));
            $where .= "WHERE type IN ('{$feedtype_string}')";
        } elseif(is_string($feedtype) && !empty($feedtype)) {
            $feedtype    = filter_var($feedtype, FILTER_SANITIZE_STRING);
            $where .= "WHERE type = '$feedtype' ";
        }
        return $this->wpdbObj->get_var('SELECT COUNT(*) FROM ' . $this->wpdbObj->prefix."contenthub_items " . $where);
    }

    public function make_type_selec($value)
    {
        return "type = '{$type}'";
    }

}
