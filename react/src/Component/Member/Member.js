import React from "react";
import { makeStyles } from "@material-ui/core/styles";
import { Typography } from "@material-ui/core";
import MemberCertifications from "../MemberCertifications/MemberCertifications";

const useStyles = makeStyles((theme) => ({
    paper: {
        marginTop: theme.spacing(8),
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
}));

const Member = (props) => {
    const classes = useStyles();
    const member = props.member;

    return (
        <div>
            <Typography component="h1">{member.name}</Typography>
            <ul className={classes.list}>
                <li>Viking name: {member.viking_name}</li>
                <li>Address: {member.address}</li>
                <li>City: {member.city}</li>
                <li>State: {member.state}</li>
                <li>Zip: {member.zip}</li>
                <li>Joined: {member.date_joined}</li>
            </ul>
            <MemberCertifications certifications={member.certifications} />
            {/*<MemberRanks ranks={member.ranks} />*/}
        </div>
    );
};

export default Member;