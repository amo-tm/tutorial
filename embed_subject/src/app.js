import React, { useEffect, useRef } from 'react';
import ReactDOM from 'react-dom';

import { initializeWsc, mountWsc } from '@amo-tm/wsc';

const App = () => {
  const containerRef = useRef(null);

  useEffect(() => {
    initializeWsc(window.AMO_WSC_PARAMS);
    mountWsc({
      container: containerRef.current,
      onSuccess: () => {
        console.log('success');
      },
      onError: console.error,
    });
  }, []);

  return (
    <div>
      <h1>Embed subject demo</h1>
      <div
        ref={containerRef}
        style={{
          width: 700,
          height: 800,
        }}
      />
    </div>
  );
};

const app = document.getElementById('app');
ReactDOM.render(<App />, app);
