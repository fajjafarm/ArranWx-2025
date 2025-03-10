/**
 * Theme: Osen - Responsive Bootstrap 5 Admin Dashboard
 * Author: Coderthemes
 * Module/App: Inbox
 */

import Quill from 'quill';
const quill = new Quill('#mail-compose', {
    modules: {
        toolbar: [
            [{ header: [1, 2, false] }],
            ['bold', 'italic', 'underline'],
            ['image', 'code-block'],
        ],
    },
    placeholder: 'Compose an epic...',
    theme: 'snow', // or 'bubble'
});
