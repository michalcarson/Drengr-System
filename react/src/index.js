import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import App from './App';
import * as serviceWorker from './serviceWorker';

console.log('drengr react app');
const target = document.getElementById('drengr-root');
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
