<?php
/**
* Copyright 2011 HubSpot, Inc.
*
*   Licensed under the Apache License, Version 2.0 (the
* "License"); you may not use this file except in compliance
* with the License.
*   You may obtain a copy of the License at
*
*       http://www.apache.org/licenses/LICENSE-2.0
*
*   Unless required by applicable law or agreed to in writing,
* software distributed under the License is distributed on an
* "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied.  See the License for the specific
* language governing permissions and limitations under the
* License.
*/
require_once('class.baseclient.php');

class HubSpot_COSBlog extends HubSpot_BaseClient {
    //Client for HubSpot Blog API.

    //Define required client variables
    protected $API_PATH = 'content/api';
    protected $API_VERSION = 'v2';

    /**
    * Get blogs from a given HubSpot portal, as identified by its API key
    *
    * @param params: Array of Blog API query filters and values
    *                See http://docs.hubapi.com/wiki/Blog_API_Methods for valid filters and values
    * @param content_type: Can be either "json" or "atom" depending on if you want JSON or XML returned.
    *
    * @returns list of blogs in JSON or XML format.
    *
    * @throws HubSpot_Exception
    **/
    public function get_blogs($params, $content_type) {
        $endpoint = 'blogs';

        if ($content_type == 'json') {
            try {
                $request_tmp = json_decode($this->execute_get_request($this->get_request_url($endpoint,$params)));
                $request_return = array();

//                JError::raiseNotice('error_message', '<pre>'.print_r($request_tmp,true).'</pre>');

                if(count($request_tmp) > 0) {
                    foreach($request_tmp->objects as $mobject) {
                        $request_return[] = (object)array('guid' => $mobject->id, 'blogTitle' => $mobject->body->public_title);
                    }
                }


                return $request_return;
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else if ($content_type == 'atom') {
            try {
                return $this->execute_get_request($this->get_request_url($endpoint,$params));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else {
            throw new HubSpot_Exception('Invalid content type, please choose either "json" or "atom"');
        }
    }

    /**
    * Get information about a specific blog
    *
    * @param params: Array of Blog API query filters and values
    *                See http://docs.hubapi.com/wiki/Blog_API_Methods for valid filters and values.
    * @param guid: The blog guid that you're looking for information about.
    * @param content_type: Can be either "json" or "atom" depending on if you want JSON or XML returned.
    *
    * @returns list of blogs in JSON or XML format.
    *
    * @throws HubSpot_Exception
    **/
    public function get_blog($params, $guid, $content_type) {
        $endpoint = $guid . '.' . $content_type;

        if ($content_type == 'json') {
            try {
                return json_decode($this->execute_get_request($this->get_request_url($endpoint, $params)));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else if ($content_type == 'atom') {
            try {
                return $this->execute_get_request($this->get_request_url($endpoint, $params));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else {
            throw new HubSpot_Exception('Invalid content type, please choose either "json" or "atom"');
        }
    }

    /**
    * Get posts from a specific blog
    *
    * @param params: Array of Blog API query filters and values
    *                See http://docs.hubapi.com/wiki/Blog_API_Methods for valid filters and values.
    * @param guid: The blog guid that you're getting posts from.
    * @param content_type: Can be either "json" or "atom" depending on if you want JSON or XML returned.
    *
    * @returns list of blogs in JSON or XML format.
    *
    * @throws HubSpot_Exception
    **/
    public function get_posts($params, $guid, $content_type) {
//        $endpoint = 'blog-posts&content_group_id='.$guid;
        $endpoint = 'blog-posts';
        $params['content_group_id'] = $guid;

        if ($content_type == 'json') {
            try {
                $hub_url = $this->get_request_url($endpoint, $params);
                $tmpReturn = json_decode($this->execute_get_request($hub_url));
             //   JError::raiseNotice('error_message',print_r($myReturn,true).$endpoint);
//                echo print_r($tmpReturn,true).$hub_url;
                $myReturn = array();
                foreach($tmpReturn->objects as $key => $object) {
	                if($object->processing_status == 'published') {
	                    $myReturn[$key] = new stdClass();;
	                    $myReturn[$key]->title = $object->html_title;
		                $myReturn[$key]->url = $object->url;
		                $myReturn[$key]->body = $object->post_body;
		                $myReturn[$key]->authorDisplayName = $object->author_name;
		                $myReturn[$key]->createTimestamp = $object->created;
		                $myReturn[$key]->summary = $object->post_summary;
		                $myReturn[$key]->postAnalytics = new stdClass();
		                $myReturn[$key]->postAnalytics->comments = $object->comment_count;
	                }
                }
                return $myReturn;
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else if ($content_type == 'atom') {
            try {
                return $this->execute_get_request($this->get_request_url($endpoint, $params));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else {
            throw new HubSpot_Exception('Invalid content type, please choose either "json" or "atom"');
        }
    }

    /**
    * Get all comments for a specific blog
    *
    * @param params: Array of Blog API query filters and values
    *                See http://docs.hubapi.com/wiki/Blog_API_Methods for valid filters and values.
    * @param guid: The blog guid that you're getting comments from.
    * @param content_type: Can be either "json" or "atom" depending on if you want JSON or XML returned.
    *
    * @returns list of blogs in JSON or XML format.
    *
    * @throws HubSpot_Exception
    **/
    public function get_comments($params, $guid, $content_type) {
        $endpoint = $guid . '/comments.' . $content_type;

        if ($content_type == 'json') {
            try {
                return json_decode($this->execute_get_request($this->get_request_url($endpoint, $params)));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else if ($content_type == 'atom') {
            try {
                return $this->execute_get_request($this->get_request_url($endpoint, $params));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else {
            throw new HubSpot_Exception('Invalid content type, please choose either "json" or "atom"');
        }
    }

    /**
    * Get information about a specific blog post.
    *
    * @param params: Array of Blog API query filters and values
    *                See http://docs.hubapi.com/wiki/Blog_API_Methods for valid filters and values.
    * @param guid: The blog post guid that you're getting information about.
    * @param content_type: Can be either "json" or "atom" depending on if you want JSON or XML returned.
    *
    * @returns list of blogs in JSON or XML format.
    *
    * @throws HubSpot_Exception
    **/
    public function get_post($params, $guid, $content_type) {
        $endpoint = 'posts/' . $guid . '.' . $content_type;

        if ($content_type == 'json') {
            try {
                return json_decode($this->execute_get_request($this->get_request_url($endpoint, $params)));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else if ($content_type == 'atom') {
            try {
                return $this->execute_get_request($this->get_request_url($endpoint, $params));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else {
            throw new HubSpot_Exception('Invalid content type, please choose either "json" or "atom"');
        }
    }

    /**
    * Get comments for a specific blog post.
    *
    * @param params: Array of Blog API query filters and values
    *                See http://docs.hubapi.com/wiki/Blog_API_Methods for valid filters and values.
    * @param guid: The blog post guid that you're getting comments from.
    * @param content_type: Can be either "json" or "atom" depending on if you want JSON or XML returned.
    *
    * @returns list of blogs in JSON or XML format.
    *
    * @throws HubSpot_Exception
    **/
    public function get_post_comments($params, $post_guid, $content_type) {
        $endpoint = 'posts/' . $post_guid . '/comments.' . $content_type;

        if ($content_type == 'json') {
            try {
                return json_decode($this->execute_get_request($this->get_request_url($endpoint, $params)));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else if ($content_type == 'atom') {
            try {
                return $this->execute_get_request($this->get_request_url($endpoint, $params));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else {
            throw new HubSpot_Exception('Invalid content type, please choose either "json" or "atom"');
        }
    }

    /**
    * Get information about a specific comment.
    *
    * @param params: Array of Blog API query filters and values
    *                See http://docs.hubapi.com/wiki/Blog_API_Methods for valid filters and values.
    * @param guid: The comment guid that you're getting information about.
    * @param content_type: Can be either "json" or "atom" depending on if you want JSON or XML returned.
    *
    * @returns list of blogs in JSON or XML format.
    *
    * @throws HubSpot_Exception
    **/
    public function get_comment($params, $comment_guid, $content_type) {
        $endpoint = 'comments/' . $comment_guid . '.' . $content_type;

        if ($content_type == 'json') {
            try {
                return json_decode($this->execute_get_request($this->get_request_url($endpoint, $params)));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else if ($content_type == 'atom') {
            try {
                return $this->execute_get_request($this->get_request_url($endpoint, $params));
            } catch (HubSpot_Exception $e) {
                throw new HubSpot_Exception('Unable to retrieve blogs: ' . $e);
            }
        } else {
            throw new HubSpot_Exception('Invalid content type, please choose either "json" or "atom"');
        }
    }


    /**
    * Create a blog post in draft mode (not published)
    *
    * @param blog_guid: The blog guid that you're creating the new post in.
    * @param author_name: The author name for the post you're creating.
    * @param author_email: The author email for the post you're creating. Please note that this email MUST be a vaild user
    *       in the HubSpot portal you're creating the blog post in.
    * @param title: The title of the blog post you're creating.
    * @param summary: The summary of the blog post you're creating.
    * @param post_content: The blog post itself that you're creating. Can be HTML.
    * @param tags: An array of tags that you want to tag the blog post which you're creating. This must be an array,
    *           see "example.php" for an example.
    *
    * @returns Body of POST request
    *
    * @throws HubSpot_Exception
    **/
    public function create_post($blog_guid, $author_name, $author_email, $title, $summary, $post_content, $tags) {
        $endpoint = $blog_guid . '/posts.atom';

        if ($this->isBlank($title)) {
            throw new HubSpot_Exception('Blog title is required!');
        } else if ($this->isBlank($post_content)) {
            throw new HubSpot_Exception('Blog content is required!');
        } else if ($this->isBlank($author_email)) {
            throw new HubSpot_Exception('Author email is required!');
        }

        $tag_to_input = '';
        foreach ($tags as $tag) {
            $tag_to_input = $tag_to_input . '<category term="'. $tag .'" />';
        }

        $body = '<?xml version="1.0" encoding="utf-8"?>
            <entry xmlns="http://www.w3.org/2005/Atom">
              <title>' . $title . '</title>
                 <author>
                   <name>' . $author_name . '</name>
                   <email>' . $author_email .'</email>
                 </author>
                 <summary>' . $summary . '</summary>
                 <content type="html"><![CDATA['.$post_content.']]></content>
                 '. $tag_to_input .'
            </entry>';

        try {
            return $this->execute_xml_post_request($this->get_request_url($endpoint,null), $body);
        } catch (HubSpot_Exception $e) {
            throw new HubSpot_Exception('Unable to add blog post: ' . $e);
        }
    }

    /**
    * Update a blog post
    *
    * @param post_guid: The blog post guid that you're getting information about.
    * @param title: The title of the blog post you're creating.
    * @param summary: The summary of the blog post you're creating.
    * @param post_content: The blog post itself that you're creating.
    * @param tags: An array of tags that you want to tag the blog post which you're creating. This must be an array,
    *           see "example.php" for an example.
    * @param meta_desc: The meta description that you want to update (or create)
    * @param keywords: An array of meta keywords that you want to update or create anew.
    *
    * @returns Body of POST request
    *
    * @throws HubSpot_Exception
    **/
    public function update_post($post_guid, $title, $summary, $post_content, $tags, $meta_desc, $keywords) {
        $endpoint = 'posts/' . $post_guid . '.atom';

        if ($this->isBlank($post_guid)) {
            throw new HubSpot_Exception('Post guid is required!');
        }

        $tag_to_input = '';
        foreach ($tags as $tag) {
            $tag_to_input = $tag_to_input . '<category term="'. $tag .'" />';
        }
        $keywords_to_input = '';
        foreach ($keywords as $word) {
            $keywords_to_input = $keywords_to_input . $word . ',';
        }

        $body = '<?xml version="1.0" encoding="utf-8"?>
        <entry xmlns="http://www.w3.org/2005/Atom" xmlns:hs="http://www.hubspot.com/">
            <title>'. $title .'</title>
            <summary>'. $summary .'</summary>
            <content type="text">'. $post_content .'</content>
            '. $tag_to_input .'
            <hs:metaDescription>'. $meta_desc .'</hs:metaDescription>
            <hs:metaKeywords>'. $keywords_to_input .'</hs:metaKeywords>
        </entry>';
        //echo $body;

        try {
            return $this->execute_xml_put_request($this->get_request_url($endpoint,null), $body);
        } catch (HubSpot_Exception $e) {
            throw new HubSpot_Exception('Unable to update blog post: ' . $e);
        }
    }

    /**
    * Publish a blog post (making its state go from draft to published)
    *
    * @param post_guid: The blog post guid that you're publishing.
    * @param publish_time: This is an ISO-8601 formatted date string in UTC time (i.e. 2011-03-13T18:30:02Z). See example.php for help.
    * @param should_notify: If false then email and social media notifications will be not be sent. If not specified or explicitly set
    *           to true, then the email and social media notifications will go out as normal.
    *
    * @returns Body of POST request
    *
    * @throws HubSpot_Exception
    **/
    public function publish_post($post_guid, $publish_time, $should_notify) {
        $endpoint = 'posts/' . $post_guid . '.atom';

        if ($this->isBlank($post_guid)) {
            throw new HubSpot_Exception('Post guid is required!');
        }

        $body = '<?xml version="1.0" encoding="utf-8"?>
        <entry xmlns="http://www.w3.org/2005/Atom" xmlns:hs="http://www.hubspot.com/">
            <published>'. $publish_time .'</published>
            <hs:draft>false</hs:draft>
            <hs:sendNotifications>'. $should_notify .'</hs:sendNotifications>
        </entry>';

        try {
            return $this->execute_xml_put_request($this->get_request_url($endpoint,null), $body);
        } catch (HubSpot_Exception $e) {
            throw new HubSpot_Exception('Unable to publish blog post: ' . $e);
        }
    }

    /**
    * Create a blog comment in a blog post
    *
    * @param post_guid: The blog post guid for the comment that you're publishing.
    * @param author_name: The author name for the comment you're creating.
    * @param author_email: The author email for the comment you're creating.
    * @param url: The website URL for the comment author.
    * @param comment: The comment text, can be HTML.
    *
    * @returns Body of POST request
    *
    * @throws HubSpot_Exception
    **/
    public function create_comment($post_guid, $author_name, $author_email, $url, $comment) {
        $endpoint = 'posts/' . $post_guid . '/comments.atom';

        $body = '<?xml version="1.0" encoding="utf-8"?>
        <entry xmlns="http://www.w3.org/2005/Atom">
          <author>
            <name>'.$author_name.'</name>
            <email>'.$author_email.'</email>
            <uri>'.$url.'</uri>
          </author>
          <content><![CDATA['.$comment.']]></content>
        </entry>';

        try {
            return $this->execute_xml_post_request($this->get_request_url($endpoint,null), $body);
        } catch (HubSpot_Exception $e) {
            throw new HubSpot_Exception('Unable to add blog post: ' . $e);
        }
    }
}