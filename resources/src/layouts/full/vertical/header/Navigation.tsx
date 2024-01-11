import { useState } from 'react';
import { Box, Menu, Typography, Button, Divider, Grid } from '@mui/material';
import { Link } from 'react-router-dom';
import { IconChevronDown, IconHelp } from '@tabler/icons';
import AppLinks from './AppLinks';
import QuickLinks from './QuickLinks';
import React from 'react';

const AppDD = () => {
  const [anchorEl2, setAnchorEl2] = useState(null);

  const handleClick2 = (event: any) => {
    setAnchorEl2(event.currentTarget);
  };

  const handleClose2 = () => {
    setAnchorEl2(null);
  };

  return (
    <>
      {/* <Button color="inherit" sx={{color: (theme) => theme.palette.text.secondary}} variant="text" to="/apps/chats" component={Link}>
        Chat
      </Button>
      <Button color="inherit" sx={{color: (theme) => theme.palette.text.secondary}} variant="text" to="/apps/calendar" component={Link}>
        Calendar
      </Button> */}
    </>
  );
};

export default AppDD;
