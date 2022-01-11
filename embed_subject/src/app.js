import { initializeWsc, mountWsc } from '@amo-tm/wsc';

initializeWsc(window.AMO_WSC_PARAMS);
mountWsc({
    container: "#amo-subject-container"
});