import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import * as serviceWorker from './serviceWorker';

let target = document.getElementById('drengr-root');
if (process.env.NODE_ENV === 'development') {
    target = document.getElementById('root');
}

if (target) {
    ReactDOM.render(
        <React.StrictMode>
            <App/>
        </React.StrictMode>,
        target
    );
}

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
serviceWorker.unregister();
