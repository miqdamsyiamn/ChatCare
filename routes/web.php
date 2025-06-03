<?php
/**
 * Web Routes
 * 
 * File ini mendefinisikan semua route untuk aplikasi web
 */

// Define routes
$routes = [
    // Home and Auth routes
    '' => ['controller' => 'HomeController', 'method' => 'index'],
    'home' => ['controller' => 'HomeController', 'method' => 'index'],
    'login' => ['controller' => 'AuthController', 'method' => 'login'],
    'register' => ['controller' => 'AuthController', 'method' => 'register'],
    'logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    
    // Dashboard routes
    'dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
    
    // User management routes (Admin only)
    'users' => ['controller' => 'UserController', 'method' => 'index'],
    'users/create' => ['controller' => 'UserController', 'method' => 'create'],
    'users/store' => ['controller' => 'UserController', 'method' => 'store'],
    'users/edit' => ['controller' => 'UserController', 'method' => 'edit'],
    'users/update' => ['controller' => 'UserController', 'method' => 'update'],
    'users/delete' => ['controller' => 'UserController', 'method' => 'delete'],
    
    // Discussion routes (Leader)
    'discussions' => ['controller' => 'DiscussionController', 'method' => 'index'],
    'discussions/create' => ['controller' => 'DiscussionController', 'method' => 'create'],
    'discussions/store' => ['controller' => 'DiscussionController', 'method' => 'store'],
    'discussions/edit' => ['controller' => 'DiscussionController', 'method' => 'edit'],
    'discussions/update' => ['controller' => 'DiscussionController', 'method' => 'update'],
    'discussions/delete' => ['controller' => 'DiscussionController', 'method' => 'delete'],
    'discussions/members' => ['controller' => 'DiscussionController', 'method' => 'members'],
    'discussions/add-member' => ['controller' => 'DiscussionController', 'method' => 'addMember'],
    'discussions/remove-member' => ['controller' => 'DiscussionController', 'method' => 'removeMember'],
    'discussions/start' => ['controller' => 'DiscussionController', 'method' => 'start'],
    'discussions/end' => ['controller' => 'DiscussionController', 'method' => 'end'],
    
    // Chatroom routes
    'chatroom' => ['controller' => 'ChatroomController', 'method' => 'index'],
    'chatroom/send' => ['controller' => 'ChatroomController', 'method' => 'send'],
    'chatroom/messages' => ['controller' => 'ChatroomController', 'method' => 'messages'],
    
    // Mood visualization routes
    'mood' => ['controller' => 'MoodController', 'method' => 'index'],
    'mood/data' => ['controller' => 'MoodController', 'method' => 'getData'],
    
    // Report routes
    'reports' => ['controller' => 'ReportController', 'method' => 'index'],
    'reports/view' => ['controller' => 'ReportController', 'method' => 'viewReport'],
    'reports/generate' => ['controller' => 'ReportController', 'method' => 'generate'],
    
    // Settings routes
    'settings' => ['controller' => 'SettingsController', 'method' => 'index'],
    'settings/update' => ['controller' => 'SettingsController', 'method' => 'update'],
    
    // API routes for AJAX
    'api/messages' => ['controller' => 'ApiController', 'method' => 'getMessages'],
    'api/send-message' => ['controller' => 'ApiController', 'method' => 'sendMessage'],
    'api/mood-data' => ['controller' => 'ApiController', 'method' => 'getMoodData'],
    'api/generate-feedback' => ['controller' => 'ApiController', 'method' => 'generateFeedback'],
];

return $routes;
