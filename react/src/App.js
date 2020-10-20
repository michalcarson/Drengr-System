import React from 'react';
import './App.css';
import Menu from './Component/Menu/Menu';
import Splash from './Component/Splash/Splash';

function App() {
  return (
    <div className="App">
      <div className="drengr-header">
        <Menu/>
      </div>
      <div className="drengr-main">
        <Splash/>
      </div>
    </div>
  );
}

export default App;
