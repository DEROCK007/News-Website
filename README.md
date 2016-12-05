# News-Website
A website where users can add stories, write/edit comments, and search for stories by a keyword
# Link: http://ec2-54-164-126-240.compute-1.amazonaws.com/~mwindlinger/mod3index.php

Use username testuser and testpassword if you want to log in, or you can create your own user
-> Password Modifying

    - User can change his/her password freely by clicking the "Change Password" button on the index page.
    The old password will need to be typed in for verification should the user decide to change the 
    password.
-> Password Recovery

    - By clicking the "Forget Password" button on the index, the user can reset his/her password without
    signing in. Three security questions and answers were set up when the user first signed up. These
    security questions will need to be answered correctly to change password without signing in.
    - Like password, the security answers are also stored in the form of salted hash.
-> Upload / Update / Delete Picture

    - User can upload a picture along with his/her story.
    - User can update the picture while editing the story.
    - User can choose to delete the picture while editing the story.
    - If no picture is uploaded when editing, the picture will not be changed.
    - Old picture is deleted during the update process.
-> Keywords

    - User can enter up to 5 keywords/tags associated with each of his/her story.
    - By clicking a tag in the story content page, a search will be automatically trigged and a list
    of stories with similar tags will be shown.
-> Related Stories

    - On the story content page, list of stories with similar tags will be shown along with the story
    itself.
-> Fuzzy Searching

    - A search bar is provided on the top of the index/content/user profile page.
    - Using fuzzy searching scheme, the search will return stories with tags similar to the query or
    stories whose titles contain the query on the result page.
    - The search bar on the user's own story board page can be used to search for user's own stories.
-> User Story Board / User Profile

    - User has his/her own story board page to see and manage his/her story. The story board can be
    accessed by clicking the "Go to my story board" button or by clicking user's own name on the
    index page or the content page.
    - Visitor can see a list of the stories wrote by a user in the user's profile page. The user
    profile page is accessed by clicking the user's name on the index page or the content page.
-> Line Breaker

    - User can insert new line by typing ^br^ when posting or editing story.
    - The ^br^ will be stored as it is in the database.
    - When showing the story content, the ^br^ will be replaced by <br /> after escaping output and
    form a new line on the content page.
-> Story Abstract

    - On the index page or the search result page, abstracts of the stories will be shown along with
    the list of titles instead of the whole content.
    - The abstract of the story consists of the first 15 or 30 words of the story's text content.
-> Story Info

    - First published date and time of stories are shown on the index/story board/user profile page.
    - Last modification date and time and first published date and time of a story are shown on the 
    content page.
-> Enhanced Security

    - Security check feature is added to prevent unauthorized entry. Pages containing sensitive
    information/operation can only be displayed after successful login. Any unauthorized visit will 
    be redirected to the index page immediately.
    - Multiple tokens are passed in the password recovery/modification process.
-> Appealing Visual Design

    - Animations and enhanced visuals are added to make the site more user friendly.
