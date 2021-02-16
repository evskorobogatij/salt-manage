import React from "react";
import {makeStyles, fade} from "@material-ui/core/styles";
import { Theme } from '@material-ui/core/styles/createMuiTheme';
// import {Theme}  from '@material-ui/core';
// import Button from '@material-ui/core/Button';
import Toolbar from "@material-ui/core/Toolbar";
import IconButton from "@material-ui/core/IconButton";
import MenuIcon from '@material-ui/icons/Menu';
import SearchIcon from '@material-ui/icons/Search';
import Typography from "@material-ui/core/Typography";
import AppBar from "@material-ui/core/AppBar/AppBar";
import NotificationsIcon from '@material-ui/icons/Notifications';
import Badge from '@material-ui/core/Badge'
import InputBase from "@material-ui/core/InputBase";

function XAppBar() {

    const useStyles = makeStyles((theme: Theme) => ({
        appBar: {
            // flexGrow: 1,
            zIndex: theme.zIndex.drawer + 1,
        },
        menuButton: {
            marginRight: theme.spacing(2),
        },
        title: {
            flexGrow: 1,
        },
        search: {
            position: 'relative',
            borderRadius: theme.shape.borderRadius,
            backgroundColor: fade(theme.palette.common.white, 0.15),
            '&:hover': {
                backgroundColor: fade(theme.palette.common.white, 0.25),
            },
            marginLeft: 0,
            width: '100%',
            [theme.breakpoints.up('sm')]: {
                marginLeft: theme.spacing(1),
                width: 'auto',
            },
        },
        searchIcon: {
            padding: theme.spacing(0, 2),
            height: '100%',
            position: 'absolute',
            pointerEvents: 'none',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
        },
        inputRoot: {
            color: 'inherit',
        },
        inputInput: {
            padding: theme.spacing(1, 1, 1, 0),
            // vertical padding + font size from searchIcon
            paddingLeft: `calc(1em + ${theme.spacing(4)}px)`,
            transition: theme.transitions.create('width'),
            width: '100%',
            [theme.breakpoints.up('sm')]: {
                width: '12ch',
                '&:focus': {
                    width: '20ch',
                },
            },
        },

    }));

    const classes = useStyles();

    return (
        <>
            <AppBar position={"fixed"} className={classes.appBar} >
                <Toolbar>
                    <IconButton edge={"start"} className={classes.menuButton} color="inherit" aria-label="menu" >
                        <MenuIcon />
                    </IconButton>
                    <Typography variant={"h6"} className={classes.title}>
                        Панель управления
                    </Typography>

                    <div className={classes.search}>
                        <div className={classes.searchIcon}>
                            <SearchIcon />
                        </div>
                        <InputBase
                            placeholder="Поиск…"
                            classes={{
                                root: classes.inputRoot,
                                input: classes.inputInput,
                            }}
                            inputProps={{ 'aria-label': 'search' }}
                        />
                    </div>

                    <IconButton aria-label="show 4 new mails" color="inherit">
                        <Badge badgeContent={4} color="secondary">
                            <NotificationsIcon />
                        </Badge>
                    </IconButton>
                </Toolbar>
            </AppBar>
        </>
    )
}

export {XAppBar}